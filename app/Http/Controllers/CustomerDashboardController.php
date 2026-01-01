<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::guard('customer')->user();
        
        $reservations = Reservation::where('user_id', $user->id)
            ->with(['room.building', 'payment', 'checkIn'])
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'total' => $reservations->count(),
            'pending' => $reservations->where('status', 'pending')->count(),
            'active' => $reservations->whereIn('status', ['approved', 'checked_in'])->count(),
            'completed' => $reservations->where('status', 'completed')->count(),
        ];

        return view('customer.dashboard', compact('reservations', 'stats'));
    }

    public function reservation(Reservation $reservation)
    {
        $user = Auth::guard('customer')->user();
        
        // Ensure user can only view their own reservations
        if ($reservation->user_id !== $user->id) {
            abort(403);
        }

        $reservation->load(['room.building', 'payment', 'checkIn']);

        return view('customer.reservation', compact('reservation'));
    }
}
