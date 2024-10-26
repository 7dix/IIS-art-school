<?php

namespace App\Http\Controllers;

use App\Http\Resources\AtelierResource;
use App\Models\Atelier;
use Illuminate\Http\Request;

class AtelierController extends Controller
{
    public function index()
    {
        $ateliers = AtelierResource::collection(
            Atelier::with(['types', 'equipments', 'manager', 'users'])->get()
        );
        return inertia('Atelier/Index', ['ateliers' => $ateliers]);
    }
}
