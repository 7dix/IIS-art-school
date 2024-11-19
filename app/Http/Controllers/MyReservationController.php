<?php
namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Http\Resources\ReservationResource;
use Illuminate\Support\Facades\Auth;

class MyReservationController extends Controller
{
    public function index()
    {
        $userId = Auth::id(); // Get the authenticated user's ID

        $reservations = ReservationResource::collection(
            Reservation::with(['user', 'equipment'])
                ->where('user_id', $userId) // Filter by user ID
                ->get()
        );
        return inertia('MyReservation/Index', [
            'reservations' => $reservations,
        ]);
    }
}