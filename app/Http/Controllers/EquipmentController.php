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


class EquipmentController extends Controller
{
    /**
     * Display a listing of the equipment.
     *
     * @return \Illuminate\Http\Response
     */
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
            'allowed_leasing_hours' => ['required','string', 'regex:/^([8-9]|1[0-8])(,([8-9]|1[0-8]))*$/'],
            'date_of_purchase' => ['date', 'nullable'],
            'type_id' => ['required', 'exists:types,id'],
            'atelier_id' => ['required', 'exists:ateliers,id']
        ]);

        $leasing_hours = $request->input('allowed_leasing_hours');
        $leasing_hours_array = explode(',', $leasing_hours);
        $leasing_hours_array = array_map('intval', $leasing_hours_array);
        $leasing_hours_json = json_encode($leasing_hours_array);
        $validatedData['allowed_leasing_hours'] = $leasing_hours_json;

        $user = Auth::user();
        $validatedData['owner_id'] = $user->id;

        $equipment = Equipment::create($validatedData);

        return $this->index();
    }


    public function update(Request $request, $id) {

        $leasing_hours = $request->input('allowed_leasing_hours');
        $leasing_hours_array = explode(',', $leasing_hours);
        $leasing_hours_array = array_map('intval', $leasing_hours_array);
        $leasing_hours_json = json_encode($leasing_hours_array);
        $request->merge(['allowed_leasing_hours' => $leasing_hours_json]);


        $equipment = Equipment::find($id);
        $equipment->update($request->validate([
            'name' => ['required', 'string', 'max:255'],
            'maximum_leasing_period' => ['required', 'integer'],
            'year_of_manufacture' => ['integer', 'nullable'],
            'allowed_leasing_hours' => ['required', 'json'],
            'date_of_purchase' => ['date', 'nullable'],
            'type_id' => ['required', 'exists:types,id'],
            'atelier_id' => ['required', 'exists:ateliers,id']
        ]));

        return $this->index();
    }


    public function destroy($id) {
        /** @var \App\Models\User */
        $user = Auth::user();

        Equipment::destroy($id);

        return $this->index();
    }

    public function getUserEquipmentByType($typeId)
{
    $user = Auth::user();
    $ateliers = $user->ateliers->pluck('id'); // Get the IDs of the user's ateliers

    $equipment = Equipment::with(['atelier', 'owner']) // Include atelier and owner relationships
        ->where('type_id', $typeId)
        ->whereIn('atelier_id', $ateliers)
        ->get();

    return response()->json($equipment);
}

}