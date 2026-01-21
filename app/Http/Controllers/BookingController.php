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
            ->where('show_in_search', true)
            ->with(['roomTypes' => function ($query) {
                 // You might want to filter room types that have available rooms?
                 // or just show all types. Let's show all types but maybe count available rooms.
                 // For now, simple conversion.
                 $query->with('facilities');
            }])
            ->get();

        return view('booking.index', compact('buildings'));
    }

    public function rooms(Request $request)
    {
        // We query RoomTypes now, not Rooms directly for the listing
        $query = \App\Models\RoomType::query()
            ->where('is_public', true)
            ->whereHas('building', function ($q) {
                $q->where('is_active', true)->where('show_in_search', true);
            });

        if ($request->building_id) {
            $query->where('building_id', $request->building_id);
        }

        // RoomType search
        if ($request->room_type) {
             // If searching by slug/name strings from potential query params
             $query->where('name', 'like', '%' . $request->room_type . '%');
        }

        if ($request->total_guests) {
            $query->where('capacity', '>=', $request->total_guests);
        }
        
        // Eager load relationships
        $roomTypes = $query->with(['building', 'facilities'])->get();

        // Calculate availability for each RoomType
        // This is a bit more expensive but necessary for accurate counts
        $roomTypes->each(function ($type) use ($request) {
            $roomQuery = $type->rooms()
                ->where('status', 'available')
                ->where('is_daily_rentable', true);

            if ($request->check_in && $request->check_out) {
                $checkIn = Carbon::parse($request->check_in);
                $checkOut = Carbon::parse($request->check_out);

                $roomQuery->whereDoesntHave('reservations', function ($q) use ($checkIn, $checkOut) {
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
            
            $type->available_count = $roomQuery->count();
            // We need a sample room for booking logic if needed, or just use the type ID
            // Ideally we book by RoomType now? No, system books specific room.
            // We'll pick a random available room later.
            $type->sample_room_id = $roomQuery->first()?->id; 
        });
        
        // Filter out types with no availability if desired?
        // $roomTypes = $roomTypes->filter(fn($t) => $t->available_count > 0);

        $buildings = Building::where('is_active', true)
            ->where('show_in_search', true)
            ->get();

        return view('booking.rooms', compact('roomTypes', 'buildings'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'room_type' => 'required|exists:room_types,id', // Changed input to be an ID
            'building_id' => 'required|exists:buildings,id',
        ]);

        $checkIn = $request->check_in ?? now()->addDay()->format('Y-m-d');
        $checkOut = $request->check_out ?? now()->addDays(2)->format('Y-m-d');

        // Fetch RoomType details
        $roomType = \App\Models\RoomType::with(['building', 'facilities'])->findOrFail($request->room_type);
        
        // We use the roomType object as the "sample room" for display purposes
        // Adapting the view might be needed if it expects a Room object specifically, 
        // but RoomType has similar attributes (price, capacity, description, facilities, images).
        // Let's pass $roomType as $sampleRoom for compatibility, or update view variable name.
        // The view likely accesses $sampleRoom->price, $sampleRoom->facilities etc.
        // RoomType matches this signature.

        return view('booking.create', [
            'sampleRoom' => $roomType, 
            'checkIn' => $checkIn, 
            'checkOut' => $checkOut
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_type' => 'required|exists:room_types,id',
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
        $roomTypeId = $validated['room_type'];

        // Find an available room from the selected RoomType
        $availableRoom = Room::where('room_type_id', $roomTypeId)
             // building_id is theoretically redundant if room_type is scoped to building, but safe to keep
            ->where('building_id', $validated['building_id'])
            ->where('status', 'available')
            ->where('is_daily_rentable', true)
             // Active building check
            ->whereHas('building', function ($q) {
                $q->where('is_active', true)->where('show_in_search', true);
            })
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
        // Use the Category Price for public bookings to ensure consistency with the displayed price.
        // Room overrides are ignored here to prevent "bill shock" if a specific room has a different price.
        $pricePerNight = $availableRoom->roomType->price;
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
            'notes' => $validated['notes'],
            'status' => 'approved', // Auto-approve to allow immediate payment
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

    /**
     * Show building detail page with room types
     */
    public function buildingDetail(Building $building)
    {
        if (!$building->is_active || !$building->show_in_search) {
            abort(404);
        }

        // Load RoomTypes with basic info and available room count
        $roomTypes = $building->roomTypes()
             ->where('is_public', true)
             ->with(['facilities']) // Add other relations if needed
             ->get()
             ->map(function ($type) {
                 // Calculate availability
                 $availableCount = $type->rooms()
                     ->where('status', 'available')
                     ->where('is_daily_rentable', true)
                     ->count();
                 
                 // Map to format expected by view (similar to listing)
                 return [
                     'id' => $type->id, // Add ID for linking
                     'room_type' => $type->name, // View might expect string name
                     'room_type_label' => $type->name, // Or use name directly
                     'available_count' => $availableCount,
                     'price' => $type->price,
                     'capacity' => $type->capacity,
                     'facilities' => $type->facilities,
                     'description' => $type->description,
                     'image' => $type->images ? ($type->images[0] ?? null) : null, // Handle array
                 ];
             });

        return view('booking.building-detail', compact('building', 'roomTypes'));

        return view('booking.building-detail', compact('building', 'roomTypes'));
    }

    /**
     * Show room type detail page
     */
    public function roomDetail(Building $building, string $roomType)
    {
        if (!$building->is_active || !$building->show_in_search) {
            abort(404);
        }

        // $roomType could be ID (new way) or string Name (old way/search friendly)
        // Let's try to interpret it.
        $type = null;
        if (is_numeric($roomType)) {
             $type = \App\Models\RoomType::find($roomType);
        } else {
             // Fallback search by name
             $type = \App\Models\RoomType::where('building_id', $building->id)
                 ->where('name', $roomType)
                 ->first();
        }

        if (!$type) {
            abort(404);
        }

        // Mock sample room from type data
        $sampleRoom = $type; 
        $sampleRoom->facilities = $type->facilities; // load relations if not loaded?
        $type->load('facilities');
        
        $availableCount = $type->rooms()->where('status', 'available')->count();
        $roomTypeLabel = $type->name;

        // View expects 'roomType' variable which was string. passing object might verify.
        // Let's pass 'roomType' as the OBJECT, but view might expect string.
        // Check view `booking.room-detail` later.
        
        return view('booking.room-detail', [
            'building' => $building, 
            'sampleRoom' => $sampleRoom, 
            'roomType' => $type->name, // Pass name as string for legacy or display? Or keep original arg? View uses $roomTypeId for booking.
            'roomTypeId' => $type->id,
            'roomTypeLabel' => \Illuminate\Support\Str::ucfirst(str_replace('_', ' ', $type->name)),
            'availableCount' => $availableCount
        ]);
    }

    /**
     * Get human-readable room type label
     */
    /**
     * Get human-readable room type label - Deprecated/Helper
     */
    private function getRoomTypeLabel(string $type): string
    {
         return ucwords($type);
    }
}
