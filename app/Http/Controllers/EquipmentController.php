<?php

namespace App\Http\Controllers;


use App\Http\Resources\EquipmentResource;
use App\Http\Resources\AtelierResource;
use App\Http\Resources\RoleResource;
use App\Http\Resources\TypeResource;
use App\Models\Equipment;
use App\Models\User;
use App\Models\Atelier;
use App\Models\Role;
use App\Models\Type;
use App\Http\Requests\UpdateUserRequest;
// use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;


class EquipmentController extends Controller
{
    /**
     * Display a listing of the equipment.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct() {
        parent::__construct();

        $this->middleware(function ($request, $next) {
            if (!$this->user || !$this->user->can('manage_equipment')) {
                return redirect()->route('dashboard'); // Unauthorized access
            }

            return $next($request);
        });

    }
    
    public function index() {

        $equipments = EquipmentResource::collection(
            Equipment::with('type')->get()
        );

        $ateliers = AtelierResource::collection(Atelier::all());

        $types = TypeResource::collection(Type::all());
        return inertia('Equipment/Index', ['equipments' => $equipments, 'types' => $types, 'ateliers' => $ateliers]);

    }


    public function create()
    {
        $ateliers = AtelierResource::collection(Atelier::all());

        
        $types = TypeResource::collection(Type::all());
     
        return inertia('Equipment/Create', ['types' => $types, 'ateliers' => $ateliers]);
    }

    public function store(Request $request) {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:equipments,name'],
            'maximum_leasing_period' => ['required', 'integer','min:1', 'max:90'],
            'year_of_manufacture' => ['integer', 'nullable'],
            'allowed_leasing_hours' => ['required', 'array', 'min:1'],
            'allowed_leasing_hours.*' => ['integer', 'min:0', 'max:23'],
            'date_of_purchase' => ['date', 'nullable'],
            'type_id' => ['required', 'exists:types,id'],
            'atelier_id' => ['required', 'exists:ateliers,id']
        ]);

        // No need to split and convert since we're already receiving an array
        $validatedData['allowed_leasing_hours'] = json_encode(
            array_values(array_unique($request->input('allowed_leasing_hours')))
        );

        $user = Auth::user();
        $validatedData['owner_id'] = $user->id;

        $equipment = Equipment::create($validatedData);

        return $this->index();
    }


    public function update(Request $request, $id) {


        $equipment = Equipment::find($id);
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('equipments', 'name')->ignore($equipment->id),],
            'maximum_leasing_period' => ['required', 'integer','min:1', 'max:90'],
            'year_of_manufacture' => ['integer', 'nullable'],
            'allowed_leasing_hours' => ['required', 'array', 'min:1'],
            'allowed_leasing_hours.*' => ['integer', 'min:0', 'max:23'],
            'date_of_purchase' => ['date', 'nullable'],
            'type_id' => ['required', 'exists:types,id'],
            'atelier_id' => ['required', 'exists:ateliers,id']
        ]);


        // No need to split and convert since we're already receiving an array
        $validatedData['allowed_leasing_hours'] = json_encode(
            array_values(array_unique($request->input('allowed_leasing_hours')))
        );

        $equipment->update($validatedData);
        return $this->index();
    }

    public function blockEquipment($id, $value) {
        $equipment = Equipment::find($id);
        $equipment->can_be_borrowed = $value;

        $equipment->update();
        return;
    }

    public function destroy($id) {
        Equipment::destroy($id);

        return $this->index();
    }

    public function getEquipmentById($id) {
        $equipment = Equipment::find($id);
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