<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function index()
    {
        $buildings = Building::where('is_active', true)
            ->with(['rooms' => function ($query) {
                $query->where('status', 'available')
                    ->with('facilities');
            }])
            ->get();

        return view('booking.index', compact('buildings'));
    }

    public function rooms(Request $request)
    {
        $query = Room::where('status', 'available');

        if ($request->building_id) {
            $query->where('building_id', $request->building_id);
        }

        if ($request->room_type) {
            $query->where('room_type', $request->room_type);
        }

        if ($request->check_in && $request->check_out) {
            $checkIn = Carbon::parse($request->check_in);
            $checkOut = Carbon::parse($request->check_out);

            $query->whereDoesntHave('reservations', function ($q) use ($checkIn, $checkOut) {
                $q->whereIn('status', ['approved', 'checked_in'])
                    ->where(function ($q2) use ($checkIn, $checkOut) {
                        $q2->whereBetween('check_in_date', [$checkIn, $checkOut])
                            ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                            ->orWhere(function ($q3) use ($checkIn, $checkOut) {
                                $q3->where('check_in_date', '<=', $checkIn)
                                    ->where('check_out_date', '>=', $checkOut);
                            });
                    });
            });
        }

        // Group rooms by type and aggregate data
        $roomTypes = $query->with(['building', 'facilities'])
            ->get()
            ->groupBy('room_type')
            ->map(function ($rooms, $type) {
                $firstRoom = $rooms->first();
                return [
                    'room_type' => $type,
                    'available_count' => $rooms->count(),
                    'building_name' => $firstRoom->building->name,
                    'building_id' => $firstRoom->building_id,
                    'price_public' => $firstRoom->price_public,
                    'capacity' => $firstRoom->capacity,
                    'floor' => $firstRoom->floor,
                    'facilities' => $firstRoom->facilities,
                    'description' => $firstRoom->description,
                    'image' => $firstRoom->image,
                    // Store first available room ID for booking
                    'sample_room_id' => $firstRoom->id,
                ];
            });

        $buildings = Building::where('is_active', true)->get();

        return view('booking.rooms', compact('roomTypes', 'buildings'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'room_type' => 'required|string',
            'building_id' => 'required|exists:buildings,id',
        ]);

        $checkIn = $request->check_in ?? now()->addDay()->format('Y-m-d');
        $checkOut = $request->check_out ?? now()->addDays(2)->format('Y-m-d');

        // Get a sample room from this type for displaying info
        $sampleRoom = Room::where('room_type', $request->room_type)
            ->where('building_id', $request->building_id)
            ->where('status', 'available')
            ->with(['building', 'facilities'])
            ->first();

        if (!$sampleRoom) {
            return redirect()->route('booking.rooms')
                ->with('error', 'Tipe kamar tidak tersedia.');
        }

        return view('booking.create', compact('sampleRoom', 'checkIn', 'checkOut'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_type' => 'required|string',
            'building_id' => 'required|exists:buildings,id',
            'guest_names' => 'required|array|min:1',
            'guest_names.*' => 'required|string|max:255',
            'guest_identity_number' => 'required|string|max:50',
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'total_guests' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $checkIn = Carbon::parse($validated['check_in_date']);
        $checkOut = Carbon::parse($validated['check_out_date']);

        // Find an available room from the selected type and building
        $availableRoom = Room::where('room_type', $validated['room_type'])
            ->where('building_id', $validated['building_id'])
            ->where('status', 'available')
            ->whereDoesntHave('reservations', function ($q) use ($checkIn, $checkOut) {
                $q->whereIn('status', ['approved', 'checked_in'])
                    ->where(function ($q2) use ($checkIn, $checkOut) {
                        $q2->whereBetween('check_in_date', [$checkIn, $checkOut])
                            ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                            ->orWhere(function ($q3) use ($checkIn, $checkOut) {
                                $q3->where('check_in_date', '<=', $checkIn)
                                    ->where('check_out_date', '>=', $checkOut);
                            });
                    });
            })
            ->first();

        if (!$availableRoom) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Tidak ada kamar tersedia untuk tipe dan tanggal yang dipilih.');
        }

        $totalNights = $checkIn->diffInDays($checkOut);
        $pricePerNight = $availableRoom->price_public;
        $totalPrice = $totalNights * $pricePerNight;

        $user = Auth::guard('customer')->user();

        // Convert guest names array to JSON for storage
        $guestNamesJson = json_encode($validated['guest_names']);

        $reservation = Reservation::create([
            'room_id' => $availableRoom->id,
            'user_id' => $user->id,
            'guest_name' => $guestNamesJson, // Store all guest names as JSON
            'guest_phone' => $user->phone, // Get from authenticated user
            'guest_email' => $user->email, // Get from authenticated user
            'guest_identity_number' => $validated['guest_identity_number'],
            'guest_type' => 'umum', // Default value since field removed from form
            'check_in_date' => $validated['check_in_date'],
            'check_out_date' => $validated['check_out_date'],
            'total_guests' => $validated['total_guests'],
            'total_nights' => $totalNights,
            'price_per_night' => $pricePerNight,
            'total_price' => $totalPrice,
            'notes' => $validated['notes'],
            'status' => 'pending',
        ]);

        return redirect()->route('booking.success', $reservation);
    }

    public function success(Reservation $reservation)
    {
        return view('booking.success', compact('reservation'));
    }

    public function check(Request $request)
    {
        $reservation = null;
        
        if ($request->code) {
            $reservation = Reservation::where('reservation_code', $request->code)
                ->with(['room.building', 'payment', 'checkIn'])
                ->first();
        }

        return view('booking.check', compact('reservation'));
    }

    public function payment(Reservation $reservation)
    {
        // Only show payment page for approved reservations
        if (!in_array($reservation->status, ['approved', 'checked_in'])) {
            return redirect()->route('booking.check', ['code' => $reservation->reservation_code])
                ->with('error', 'Reservasi belum disetujui atau sudah dibatalkan.');
        }

        $reservation->load(['room.building', 'payment']);

        return view('booking.payment', compact('reservation'));
    }

    public function uploadPayment(Request $request, Reservation $reservation)
    {
        $validated = $request->validate([
            'payment_method' => 'required|in:transfer,cash',
            'bank_name' => 'required_if:payment_method,transfer|nullable|string|max:100',
            'account_name' => 'required_if:payment_method,transfer|nullable|string|max:100',
            'proof_file' => 'required_if:payment_method,transfer|nullable|image|max:2048',
        ]);

        $proofPath = null;
        if ($request->hasFile('proof_file')) {
            $proofPath = $request->file('proof_file')->store('payments', 'public');
        }

        // Create or update payment
        $payment = Payment::updateOrCreate(
            ['reservation_id' => $reservation->id],
            [
                'amount' => $reservation->total_price,
                'payment_method' => $validated['payment_method'],
                'bank_name' => $validated['bank_name'] ?? null,
                'account_name' => $validated['account_name'] ?? null,
                'proof_file' => $proofPath,
                'status' => 'pending',
                'paid_at' => now(),
            ]
        );

        return redirect()->route('booking.payment.success', $reservation);
    }

    public function paymentSuccess(Reservation $reservation)
    {
        $reservation->load(['payment']);
        return view('booking.payment-success', compact('reservation'));
    }
}
