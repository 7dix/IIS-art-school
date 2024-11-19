<?php

namespace App\Http\Controllers;


use App\Http\Resources\EquipmentResource;
use App\Http\Resources\RoleResource;
use App\Http\Resources\TypeResource;
use App\Models\Equipment;
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

}