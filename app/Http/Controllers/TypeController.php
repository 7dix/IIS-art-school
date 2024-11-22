<?php

namespace App\Http\Controllers;

use App\Http\Resources\TypeResource;
use App\Models\Type;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TypeController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        $types = Type::with(['equipments'])->get();
        return inertia('Types/Index', ['types' => TypeResource::collection($types)]);
    }

    public function create() {
        /** @var \App\Models\User */
        $user = Auth::user();

        return inertia('Types/Create');
    }

    public function store(Request $request) {
        /** @var \App\Models\User */
        $user = Auth::user();

        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:types,name'],
        ], [
            'name.unique' => 'This Type already exists.',
        ]);
    
        $type = Type::create($validatedData);
    
        return $this->index();
    }

    public function update(Request $request, $id) {

        /** @var \App\Models\User */
        $user = Auth::user();

       $type = Type::find($id);
       $type->update($request->validate([
        'name' => ['required', 'string', 'max:255', Rule::unique('types', 'name')->ignore($type->id),],
    ]));
       

       return $this->index();
   }

    public function destroy(Type $type)
    {
        $type->delete();
        return $this->index();
    }
}