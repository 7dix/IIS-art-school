<?php
// File: app/Http/Controllers/MyReservationController.php

namespace App\Http\Controllers;

use App\Models\Type;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyReservationController extends Controller
{
    public function index()
    {
        $userId = Auth::id(); // Get the authenticated user's ID
        $reservations = Reservation::with(['user', 'equipment'])
            ->where('user_id', $userId) // Filter by user ID
            ->get();
        
        return inertia('MyReservation/Index', [
            'reservations' => $reservations
        ]);
    }

    public function create()
    {
        $types = Type::all(); // Fetch all types
        $userId = Auth::id(); 
        return inertia('MyReservation/Create', [
            'types' => $types, 'userId' => $userId
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
}