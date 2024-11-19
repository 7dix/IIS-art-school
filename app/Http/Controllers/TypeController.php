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
}