<?php
namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Http\Resources\ReservationResource;

class ReservationController extends Controller
{
    public function index() {

        $reservations = ReservationResource::collection(
            Reservation::with(['user', 'equipment'])->get()
        );
        return inertia('Reservation/Index', [
            'reservations' => $reservations,
        ]);
    }
}