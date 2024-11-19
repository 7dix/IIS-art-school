<?php

namespace App\Http\Controllers;

use App\Http\Resources\TypeResource;
use App\Models\Type;
use Illuminate\Http\Request;

class TypeController extends Controller
{
    public function index()
    {
        $types = Type::with(['equipments', 'ateliers'])->get();
        return inertia('Types/Index', ['types' => TypeResource::collection($types)]);
    }

    public function create()
    {
        return inertia('Types/Create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:types,name'],
        ], [
            'name.unique' => 'This Type already exists.',
        ]);
    
        $type = Type::create($validatedData);
    
        return $this->index();
    }
}