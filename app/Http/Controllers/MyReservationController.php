<?php
// File: app/Http/Controllers/MyReservationController.php

namespace App\Http\Controllers;

use App\Models\Type;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Equipment;



class MyReservationController extends Controller
{
    public function transformForIndex(Reservation $reservation)
    {
        return [
            'id' => $reservation->id,
            'equipment' => $reservation->equipment->name,
            'created_at' => $reservation->created_at,
            'start_date' => $reservation->start_date,
            'end_date' => $reservation->end_date,
            'status' => $reservation->status,
        ];
    }
    public function index()
    {
        $userId = Auth::id(); // Get the authenticated user's ID
        $reservations = Reservation::with(['user', 'equipment'])
            ->where('user_id', $userId) // Filter by user ID
            ->get()
            ->map(callback: fn (Reservation $reservation) => $this->transformForIndex($reservation));
        
        return inertia('MyReservation/Index', [
            'reservations' => $reservations
        ]);
    }

    public function prepare(Request $request)
    {
        $request->validate([
            'type_id' => 'required|exists:types,id',
            'equipment_id' => 'required|exists:equipments,id',
        ]);

        Session::put('reservation.type_id', $request->type_id);
        Session::put('reservation.equipment_id', $request->equipment_id);

        return response()->json(['message' => 'Reservation prepared successfully.']);
    }

    public function create(Request $request)
    {
        $types = Type::all(); // Fetch all types
        $userId = Auth::id(); 
        $typeId = (int) $request->query('type_id', 0);
        $equipmentId = (int) $request->query('equipment_id', 0);
    
        return inertia('MyReservation/Create', [
            'types' => $types,
            'userId' => $userId,
            'typeId' => $typeId,
            'equipmentId' => $equipmentId,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'type_id' => 'required|exists:types,id',
            'equipment_id' => 'required|exists:equipments,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|string',
        ]);

        $reservation = Reservation::create([
            'user_id' => Auth::id(),
            'equipment_id' => $request->equipment_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status,
        ]);

        return redirect()->route('my-reservation.index')->with('success', 'Reservation created successfully.');
    }

    public function getUserEquipmentByType($typeId)
    {
        $user = Auth::user();
        $ateliers = $user->ateliers->pluck('id'); // Get the IDs of the user's ateliers
    
        $equipment = Equipment::with(['atelier', 'owner']) // Include atelier and owner relationships
            ->where('type_id', $typeId)
            ->whereIn('atelier_id', $ateliers)
            ->where('can_be_borrowed', true)
            ->whereDoesntHave('restrictions', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->get();
    
        return response()->json($equipment);
    }
    public function getReservations($id) {
        $equipment = Equipment::with('reservation')->find($id);
    
        if (!$equipment) {
            return response()->json(['error' => 'Equipment not found'], 404);
        }
    
        return response()->json($equipment->reservation);
    }
}