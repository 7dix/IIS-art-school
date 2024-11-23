<?php

namespace App\Http\Controllers;

use App\Http\Resources\TypeResource;
use App\Models\Type;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TypeController extends Controller
{
    public function __construct() {
        parent::__construct();

        $this->middleware(function ($request, $next) {
            if (!$this->user || !$this->user->can('manage_type')) {
                return redirect()->route('dashboard');
            }

            return $next($request);
        });

    }

    public function index() {
        $types = Type::with(['equipments'])->get();
        return inertia('Types/Index', ['types' => TypeResource::collection($types)]);
    }

    public function create() {
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

       $type = Type::find($id);
       $type->update($request->validate([
        'name' => ['required', 'string', 'max:255', Rule::unique('types', 'name')->ignore($type->id),],
    ]));
       

       return $this->index();
   }

    public function destroy(Type $type) {
        $type->delete();
        return $this->index();
    }
}