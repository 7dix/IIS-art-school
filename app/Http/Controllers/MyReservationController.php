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
        $userId = Auth::id();
        $reservations = Reservation::with(['user', 'equipment'])
            ->where('user_id', $userId)
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
        $types = Type::all();
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

    $equipmentId = $request->equipment_id;
    $startDate = new \DateTime($request->start_date);
    $endDate = new \DateTime($request->end_date);

    // Fetch existing reservations for the selected equipment
    $existingReservations = Reservation::where('equipment_id', $equipmentId)
        ->where(function ($query) use ($startDate, $endDate) {
            $query->whereBetween('start_date', [$startDate, $endDate])
                  ->orWhereBetween('end_date', [$startDate, $endDate])
                  ->orWhere(function ($query) use ($startDate, $endDate) {
                      $query->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                  });
        })
        ->whereIn('status', ['ONGOING', 'APPROVED'])
        ->get();

    if ($existingReservations->isNotEmpty()) {
        return back()->withErrors(['date_range' => 'The selected date range overlaps with an existing reservation.'])->withInput();
    }

    $reservation = Reservation::create([
        'user_id' => Auth::id(),
        'equipment_id' => $equipmentId,
        'start_date' => $request->start_date,
        'end_date' => $request->end_date,
        'status' => $request->status,
    ]);

    return redirect()->route('my-reservation.index')->with('success', 'Reservation created successfully.');
}

    public function getUserEquipmentByType($typeId)
    {
        /** @var \App\Models\User */
        $user = Auth::user();
        $ateliers = $user->ateliers->pluck('id');

        if ($user->isManager()) {
            $managedAteliers = $user->managedAteliers()->pluck('id');
            $ateliers = $ateliers->merge($managedAteliers);
        }
    
        $equipment = Equipment::with(['atelier', 'owner'])
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
        $equipment = Equipment::with(['reservation' => function($query) {
            $query->whereIn('status', ['ONGOING', 'APPROVED']);
        }])->find($id);
    
        if (!$equipment) {
            return response()->json(['error' => 'Equipment not found'], 404);
        }
    
        return response()->json($equipment->reservation);
    }
}