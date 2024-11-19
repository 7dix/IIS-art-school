<?php

namespace App\Http\Controllers;


use App\Http\Resources\EquipmentResource;
use App\Http\Resources\AtelierResource;
use App\Http\Resources\RoleResource;
use App\Http\Resources\TypeResource;
use App\Models\Equipment;
use App\Models\Atelier;
use App\Models\Role;
use App\Models\Type;
use App\Http\Requests\UpdateUserRequest;
// use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class EquipmentController extends Controller
{
    /**
     * Display a listing of the equipment.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $equipments = EquipmentResource::collection(
            Equipment::with('type')->get()
        );


        $user = Auth::user();
        $types = TypeResource::collection(Type::all());
        return inertia('Equipment/Index', ['equipments' => $equipments, 'types' => $types, 'user' => $user]);

    }


    public function create()
    {
        $types = TypeResource::collection(Type::all());
     
        return inertia('Equipment/Create', ['types' => $types]);
    }

    public function store(Request $request) {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'maximum_leasing_period' => ['required', 'integer'],
            'year_of_manufacture' => ['integer', 'nullable', 'max:'.date('Y')],
            'date_of_purchase' => ['date', 'nullable', 'max:'.date('Y-m-d')],
            'type_id' => ['required', 'exists:types,id'],
            'atelier_id' => ['required', 'exists:ateliers,id']
        ]);
        $user = Auth::user();
        $validatedData['owner_id'] = $user->id;

        $equipment = Equipment::create($validatedData);

        return $this->index();
    }

}