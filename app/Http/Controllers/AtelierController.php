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
        $types = TypeResource::collection(Type::all());

        $managers = UserResource::collection(User::whereHas('roles', function($query) {
            $query->where('name', 'manager');
        })->get());

        return inertia('Atelier/Index', ['ateliers' => $ateliers, 'types' => $types, 'managers' => $managers]);
    }

    public function create() {
        $users = UserResource::collection(User::whereHas('roles', function($query) {
            $query->where('name', 'manager');
        })->get());

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
        $atelier = Atelier::with(['users.roles'])->findOrFail($id);

        $students = $atelier->users->filter(function ($user) {
            return $user->roles->contains('name', 'user');
        });

        $teachers = $atelier->users->filter(function ($user) {
            return $user->roles->contains('name', 'teacher');
        });

        return inertia('Atelier/Dashboard', [
            'atelier' => $atelier,
            'students' => $students->values(), // Ensure collection is re-indexed
            'teachers' => $teachers->values(), // Ensure collection is re-indexed
        ]);
    }

    public function removeUser($atelierId, $userId)
    {
        $atelier = Atelier::findOrFail($atelierId);
        $atelier->users()->detach($userId);
    
        return response()->json(['message' => 'User removed from atelier successfully.']);
    }

    public function availableUsers($atelierId)
    {
        $atelier = Atelier::findOrFail($atelierId);
        $usersInAtelier = $atelier->users->pluck('id');
        $availableUsers = User::whereNotIn('id', $usersInAtelier)->get();
    
        return response()->json($availableUsers);
    }

    public function usersInAtelier($atelierId){
        $atelier = Atelier::findOrFail($atelierId);
        $usersInAtelier = $atelier->users->pluck('id');

        return response()->json($usersInAtelier);
    }

    public function addUsers(Request $request, $atelierId)
    {
        $atelier = Atelier::findOrFail($atelierId);
    
        // Validate the request
        $request->validate([
            'users' => 'required|array',
            'users.*.id' => 'required|exists:users,id',
        ]);
    
        // Attach users to the atelier
        $userIds = collect($request->input('users'))->pluck('id');
        $atelier->users()->attach($userIds);
    
        // Fetch the newly added users with the correct table alias
        $newUsers = $atelier->users()->whereIn('users.id', $userIds)->get();
    
        return response()->json(['message' => 'Users added successfully.', 'users' => $newUsers]);
    }

    public function removeTeacherRole(Request $request, $atelierId)
    {
        $atelier = Atelier::findOrFail($atelierId);
        $userId = $request->input('user_id');
    
        // Find the user and remove the teacher role
        $user = $atelier->users()->findOrFail($userId);
        $user->removeRole('teacher');
        $user->assignRole('student');
    
        return response()->json(['message' => 'Teacher role removed successfully.']);
    }


    public function update(Request $request, $id) {
        $atelier = Atelier::find($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'room' => ['required', 'string', 'max:255'],
            'manager_id' => ['required', 'exists:users,id'],
            'types' => 'required|array',
            'types.*' => 'integer|exists:types,id',
        ]);

        $atelier->update($validated);

        $atelier->types()->sync($request->types);

        return $this->index();
    }
}
