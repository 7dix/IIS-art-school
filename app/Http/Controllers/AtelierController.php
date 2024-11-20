<?php

namespace App\Http\Controllers;

use App\Http\Resources\AtelierResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\TypeResource;
use App\Models\Atelier;
use App\Models\User;
use App\Models\Type;
use Illuminate\Container\Attributes\DB;
use Illuminate\Http\Request;

class AtelierController extends Controller
{
    public function index()
    {
        $ateliers = AtelierResource::collection(
            Atelier::with(['types', 'manager', 'users'])->get()
        );
        return inertia('Atelier/Index', ['ateliers' => $ateliers]);
    }

    public function create() {
        $users = UserResource::collection(
            User::where('role_id', 3)->get() //id 3 is for managers
        );
        $types = TypeResource::collection(Type::all());
        return inertia('Atelier/Create', ['users' => $users, 'types' => $types]);
    }

    public function store(Request $request) {


        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'room' => ['required', 'string', 'max:255'],
            'manager_id' => ['required', 'exists:users,id'],
            'types' => 'required|array',
            'types.*' => 'integer|exists:types,id',
        ]);

        $atelier = Atelier::create($validated);

        $atelier->types()->attach($request->types);

       
        return $this->index();
    }


    public function getAteliersWithType(Request $request, $type_id) {
        $ateliers = Atelier::whereHas('types', function($query) use ($type_id) {
            $query->where('types.id', $type_id);
        })->get();
        return AtelierResource::collection($ateliers);
    }

    public function dashboard($id)
    {
        $atelier = Atelier::findOrFail($id);
        return inertia('Atelier/Dashboard', ['atelier' => $atelier]);
    }
}
