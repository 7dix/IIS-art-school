<?php
namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Http\Resources\ReservationResource;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class ReservationController extends Controller
{
    private function transformForIndex($reservation)
    {
        return [
            'id' => $reservation->id,
            'equipment' => $reservation->equipment->name,
            'user' => $reservation->user->name,
            'created_at' => $reservation->created_at,
            'start_date' => $reservation->start_date,
            'end_date' => $reservation->end_date,
            'status' => $reservation->status,
        ];
    }
    public function index() {

        if (!$this->user->can('manage_reservation')) {
            return redirect()->route('my-reservation.index');
        }
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

    public function reservationStateUpdate(Request $request, Reservation $reservation)
    {
        $status = $request->input('status');
        if ($status === 'approved' || $status === 'rejected') {
            if ($reservation->status !== 'pending') {
                return response()->json(['message' => 'Invalid status change'], 400);
            }
        } else if ($status === 'ongoing') {
            if ($reservation->status !== 'approved') {
                return response()->json(['message' => 'Invalid status change'], 400);
            }
        } else if ($status === 'completed') {
            if ($reservation->status !== 'ongoing') {
                return response()->json(['message' => 'Invalid status change'], 400);
            }
        } else {
            return response()->json(['message' => 'Invalid status'], 400);
        }

        $reservation->status = $status;
        $reservation->save();

        return response()->json(['message' => 'Reservation status updated']);
    }
}