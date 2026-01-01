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
        $query = Room::where('status', 'available')
            ->with(['building', 'facilities']);

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

        $rooms = $query->get();
        $buildings = Building::where('is_active', true)->get();

        return view('booking.rooms', compact('rooms', 'buildings'));
    }

    public function create(Room $room, Request $request)
    {
        $checkIn = $request->check_in ?? now()->addDay()->format('Y-m-d');
        $checkOut = $request->check_out ?? now()->addDays(2)->format('Y-m-d');

        return view('booking.create', compact('room', 'checkIn', 'checkOut'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'guest_name' => 'required|string|max:255',
            'guest_phone' => 'required|string|max:20',
            'guest_email' => 'required|email|max:100',
            'guest_identity_number' => 'required|string|max:50',
            'guest_type' => 'required|in:mahasiswa,staf,dosen,umum',
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'total_guests' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $room = Room::findOrFail($validated['room_id']);
        
        $checkIn = Carbon::parse($validated['check_in_date']);
        $checkOut = Carbon::parse($validated['check_out_date']);
        $totalNights = $checkIn->diffInDays($checkOut);
        $pricePerNight = $room->price_public;
        $totalPrice = $totalNights * $pricePerNight;

        $reservation = Reservation::create([
            'room_id' => $validated['room_id'],
            'user_id' => Auth::guard('customer')->id(), // Link to logged-in user
            'guest_name' => $validated['guest_name'],
            'guest_phone' => $validated['guest_phone'],
            'guest_email' => $validated['guest_email'],
            'guest_identity_number' => $validated['guest_identity_number'],
            'guest_type' => $validated['guest_type'],
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
