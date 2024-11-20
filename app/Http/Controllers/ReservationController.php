<?php
namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Http\Resources\ReservationResource;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    private function transformForIndex($reservation)
    {
        return [
            'id' => $reservation->id,
            'equipment' => $reservation->equipment->name,
            'user' => $reservation->user->first_name . ' ' . $reservation->user->last_name,
            'created_at' => $reservation->created_at,
            'start_date' => $reservation->start_date,
            'end_date' => $reservation->end_date,
            'status' => $reservation->status,
        ];
    }
    public function index() {

        $reservations = Reservation::with(['user', 'equipment'])
            ->whereHas('equipment', function ($query) {
                $query->where('owner_id', Auth::user()->id);
            })
            ->get()
            ->map(fn ($reservation) => $this->transformForIndex($reservation));

        return inertia('Reservation/Index', [
            'reservations' => $reservations,
        ]);
    }

    public function show(Reservation $reservation)
    {
        return inertia('Reservation/Show', [
            'reservation' => $reservation->load(['user', 'equipment', 'equipment.atelier', 'equipment.type','equipment.owner']),
        ]);
    }
}