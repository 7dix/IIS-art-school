<?php

namespace App\Http\Controllers;

use App\Http\Resources\TypeResource;
use App\Models\Type;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TypeController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        if (!$user->can('create_type')) {
            return back();
        }

        $types = Type::with(['equipments', 'ateliers'])->get();
        return inertia('Types/Index', ['types' => TypeResource::collection($types)]);
    }

    public function create() {
        /** @var \App\Models\User */
        $user = Auth::user();

        if (!$user->can('create_type')) {
            return back();
        }

        return inertia('Types/Create');
    }

    public function store(Request $request) {
        /** @var \App\Models\User */
        $user = Auth::user();

        if (!$user->can('create_type')) {
            return back();
        }

        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:types,name'],
        ], [
            'name.unique' => 'This Type already exists.',
        ]);
    
        $type = Type::create($validatedData);
    
        return $this->index();
    }

    public function destroy(Type $type)
    {
        $type->delete();
        return $this->index();
    }
}