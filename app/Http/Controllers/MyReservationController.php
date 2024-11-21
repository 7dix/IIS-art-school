<?php

namespace App\Http\Controllers;

use App\Http\Resources\TypeResource;
use App\Models\Type;
use App\Models\Reservation;
use App\Http\Resources\ReservationResource;
use Illuminate\Support\Facades\Auth;
use Laravel\Pail\ValueObjects\Origin\Console;

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

    public function create()
    {
        $types = Type::all(); // Fetch all types
        return inertia('MyReservation/Create', [
            'types' => $types
        ]);
    }
}