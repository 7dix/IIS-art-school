<?php

namespace App\Http\Controllers;

use App\Http\Resources\AtelierResource;
use App\Http\Resources\UserResource;
use App\Models\Atelier;
use App\Models\User;
use Illuminate\Container\Attributes\DB;
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

    public function create() {
        $users = UserResource::collection(
            User::where('role_id', 2)->get()
        );
        return inertia('Atelier/Create', ['users' => $users]);
    }

    public function store(Request $request) {
        $atelier = Atelier::create($request->validate([
            'name' => ['required', 'string', 'max:255'],
            'room' => ['required', 'string', 'max:255'],
            'manager_id' => ['required', 'exists:users,id'],
        ]));
       
        return $this->index();
    }
}
