<?php
namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Http\Resources\ReservationResource;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    public function index() {

        $reservations = ReservationResource::collection(
            Reservation::with(['user', 'equipment'])
                ->whereHas('equipment', function ($query) {
                    $query->where('owner_id', Auth::user()->id);
                })
                ->get()
        );
        return inertia('Reservation/Index', [
            'reservations' => $reservations,
        ]);
    }
}