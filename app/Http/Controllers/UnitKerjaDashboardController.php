<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UnitKerjaDashboardController extends Controller
{
    public function index()
    {
        $unitKerja = Auth::guard('unit_kerja')->user();
        
        $reservations = Reservation::where('unit_kerja_id', $unitKerja->id)
            ->with(['room.building', 'payment'])
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'total' => $reservations->count(),
            'pending' => $reservations->where('status', 'pending')->count(),
            'active' => $reservations->whereIn('status', ['approved', 'checked_in'])->count(),
            'completed' => $reservations->where('status', 'completed')->count(),
            'total_amount' => $reservations->sum('total_price'),
            'pending_billing' => $reservations->filter(function ($r) {
                return $r->status === 'completed' && (!$r->payment || $r->payment->status === 'pending');
            })->sum('total_price'),
        ];

        return view('unit-kerja.dashboard', compact('unitKerja', 'reservations', 'stats'));
    }

    public function rooms(Request $request)
    {
        $unitKerja = Auth::guard('unit_kerja')->user();

        $query = Room::where('status', 'available')
            ->with(['building', 'facilities']);

        if ($request->building_id) {
            $query->where('building_id', $request->building_id);
        }

        if ($request->check_in && $request->check_out) {
            $checkIn = Carbon::parse($request->check_in);
            $checkOut = Carbon::parse($request->check_out);

            $query->whereDoesntHave('reservations', function ($q) use ($checkIn, $checkOut) {
                $q->whereIn('status', ['approved', 'checked_in'])
                    ->where(function ($q2) use ($checkIn, $checkOut) {
                        $q2->whereBetween('check_in_date', [$checkIn, $checkOut])
                            ->orWhereBetween('check_out_date', [$checkIn, $checkOut]);
                    });
            });
        }

        $rooms = $query->get();
        $buildings = \App\Models\Building::where('is_active', true)->get();

        return view('unit-kerja.rooms', compact('rooms', 'buildings', 'unitKerja'));
    }

    public function createBooking(Room $room, Request $request)
    {
        $unitKerja = Auth::guard('unit_kerja')->user();
        $checkIn = $request->check_in ?? now()->addDay()->format('Y-m-d');
        $checkOut = $request->check_out ?? now()->addDays(2)->format('Y-m-d');

        return view('unit-kerja.booking-create', compact('room', 'unitKerja', 'checkIn', 'checkOut'));
    }

    public function storeBooking(Request $request)
    {
        $unitKerja = Auth::guard('unit_kerja')->user();

        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'guest_name' => 'required|string|max:255',
            'guest_phone' => 'required|string|max:20',
            'guest_email' => 'nullable|email|max:100',
            'purpose' => 'required|string|max:500',
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'total_guests' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $room = Room::findOrFail($validated['room_id']);
        
        $checkIn = Carbon::parse($validated['check_in_date']);
        $checkOut = Carbon::parse($validated['check_out_date']);
        $totalNights = $checkIn->diffInDays($checkOut);
        $pricePerNight = $room->price_internal; // Internal price for unit kerja
        $totalPrice = $totalNights * $pricePerNight;

        $reservation = Reservation::create([
            'room_id' => $validated['room_id'],
            'unit_kerja_id' => $unitKerja->id,
            'guest_name' => $validated['guest_name'],
            'guest_phone' => $validated['guest_phone'],
            'guest_email' => $validated['guest_email'],
            'guest_type' => 'unit_kerja',
            'check_in_date' => $validated['check_in_date'],
            'check_out_date' => $validated['check_out_date'],
            'total_guests' => $validated['total_guests'],
            'total_nights' => $totalNights,
            'price_per_night' => $pricePerNight,
            'total_price' => $totalPrice,
            'notes' => "Keperluan: {$validated['purpose']}\n" . ($validated['notes'] ?? ''),
            'status' => 'pending',
        ]);

        return redirect()->route('unit-kerja.dashboard')
            ->with('success', "Reservasi {$reservation->reservation_code} berhasil dibuat. Menunggu persetujuan admin.");
    }

    // Bulk Booking Methods
    public function bulkBookingSearch()
    {
        $unitKerja = Auth::guard('unit_kerja')->user();
        $buildings = Building::where('is_active', true)->get();
        return view('unit-kerja.bulk-booking.search', compact('unitKerja', 'buildings'));
    }

    public function bulkBookingSelect(Request $request)
    {
        $request->validate([
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'building_id' => 'nullable|exists:buildings,id',
        ]);

        $unitKerja = Auth::guard('unit_kerja')->user();
        $checkIn = Carbon::parse($request->check_in_date);
        $checkOut = Carbon::parse($request->check_out_date);

        $query = Room::where('status', 'available')
            ->whereDoesntHave('reservations', function ($q) use ($checkIn, $checkOut) {
                $q->whereIn('status', ['approved', 'checked_in'])
                    ->where(function ($q2) use ($checkIn, $checkOut) {
                        $q2->whereBetween('check_in_date', [$checkIn, $checkOut])
                            ->orWhereBetween('check_out_date', [$checkIn, $checkOut]);
                    });
            });

        if ($request->building_id) {
            $query->where('building_id', $request->building_id);
        }

        $rooms = $query->with('building')->orderBy('building_id')->orderBy('room_number')->get();
        
        // Group rooms by Building -> Floor
        $groupedRooms = $rooms->groupBy(function ($room) {
            return $room->building->name;
        })->map(function ($buildingRooms) {
            return $buildingRooms->groupBy('floor');
        });

        return view('unit-kerja.bulk-booking.select', compact('unitKerja', 'groupedRooms', 'checkIn', 'checkOut'));
    }

    public function storeBulkBooking(Request $request)
    {
        $unitKerja = Auth::guard('unit_kerja')->user();

        $validated = $request->validate([
            'room_ids' => 'required|array|min:1',
            'room_ids.*' => 'exists:rooms,id',
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'purpose' => 'required|string|max:500',
            'guest_name' => 'nullable|string|max:255',
            'guest_phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
        ]);

        $checkIn = Carbon::parse($validated['check_in_date']);
        $checkOut = Carbon::parse($validated['check_out_date']);
        $totalNights = $checkIn->diffInDays($checkOut);
        
        $rooms = Room::whereIn('id', $validated['room_ids'])->get();

        DB::transaction(function () use ($rooms, $validated, $unitKerja, $checkIn, $checkOut, $totalNights) {
            foreach ($rooms as $room) {
                $pricePerNight = $room->price_internal;
                $totalPrice = $totalNights * $pricePerNight;

                Reservation::create([
                    'room_id' => $room->id,
                    'unit_kerja_id' => $unitKerja->id,
                    'guest_name' => $validated['guest_name'] ?? "Tamu {$unitKerja->name}", // Default name if empty
                    'guest_phone' => $validated['guest_phone'] ?? $unitKerja->phone ?? '-',
                    'guest_email' => $unitKerja->email, // Use Unit Kerja email
                    'guest_type' => 'unit_kerja',
                    'check_in_date' => $validated['check_in_date'],
                    'check_out_date' => $validated['check_out_date'],
                    'total_guests' => $room->capacity, // Max capacity
                    'total_nights' => $totalNights,
                    'price_per_night' => $pricePerNight,
                    'total_price' => $totalPrice,
                    'notes' => "Bulk Booking: {$validated['purpose']}\n" . ($validated['notes'] ?? ''),
                    'status' => 'pending',
                ]);
            }
        });

        return redirect()->route('unit-kerja.dashboard')
            ->with('success', count($rooms) . " kamar berhasil direservasi untuk periode " . $checkIn->format('d M') . " - " . $checkOut->format('d M Y') . ".");
    }

    public function reservation(Reservation $reservation)
    {
        $unitKerja = Auth::guard('unit_kerja')->user();
        
        if ($reservation->unit_kerja_id !== $unitKerja->id) {
            abort(403);
        }

        $reservation->load(['room.building', 'payment', 'checkIn']);

        return view('unit-kerja.reservation', compact('reservation', 'unitKerja'));
    }

    public function billings()
    {
        $unitKerja = Auth::guard('unit_kerja')->user();

        $billings = Payment::whereHas('reservation', function ($q) use ($unitKerja) {
            $q->where('unit_kerja_id', $unitKerja->id);
        })->with('reservation.room.building')
          ->orderBy('created_at', 'desc')
          ->get();

        return view('unit-kerja.billings', compact('billings', 'unitKerja'));
    }
}
