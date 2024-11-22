<?php
namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Http\Resources\ReservationResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class ReservationController extends Controller
{
    public function __construct() {
        parent::__construct();

        $this->middleware(function ($request, $next) {
            if (!$this->user || !$this->user->can('manage_reservation')) {
                return redirect()->route('dashboard'); // Unauthorized access
            }

            return $next($request);
        });

    }
    
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

        // Validate the request
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

        // Update the reservation status
        $reservation->status = $status;
        $reservation->save();

        return response()->json(['message' => 'Reservation status updated']);
    }
}