<?php

namespace App\Http\Controllers;

use App\Http\Resources\AtelierResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\TypeResource;
use App\Http\Resources\EquipmentResource;
use App\Models\Atelier;
use App\Models\User;
use App\Models\Type;
use Illuminate\Container\Attributes\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AtelierController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        // if (!$user->hasRole('admin') && !$user->isManager()) {
        //     return redirect()->route('dashboard')->with('error', 'You are not authorized to view this page');
        // }

        // Not dependant on user role
        $types = TypeResource::collection(Type::all());
        $users = UserResource::collection(User::whereHas('roles', function($query) {
            $query->where('name', 'user');
        })->get());

        // Admin view
        if ($user->hasRole('admin')) {
            $ateliers = AtelierResource::collection(
                Atelier::with(['manager', 'users'])->get()
            );    
            return inertia('Atelier/Index', ['ateliers' => $ateliers, 'users' => $users, 'role' => 'admin']);

        // Manager view
        } else if ($user->isManager()) {
            $ateliers = AtelierResource::collection(
                Atelier::where('manager_id', $user->id)->with(['manager', 'users'])->get()
            );
            return inertia('Atelier/Index', ['ateliers' => $ateliers, 'users' => $users, 'role' => 'manager']);
        }
        //User
        else {
            $ateliers = AtelierResource::collection(
                Atelier::whereHas('users', function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })->with(['manager', 'users'])->get()
            );
            return inertia('Atelier/Index', ['ateliers' => $ateliers, 'users' => $users, 'role' => 'manager']);
        }
    }

    public function create() {

        $users = UserResource::collection(User::all());

        return inertia('Atelier/Create', ['users' => $users]);
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:ateliers,name'],
            'room' => ['required', 'string', 'max:255', 'unique:ateliers,room', 'regex:/^([A-Z]{1}[0-9]{3})$/'],
            'manager_id' => ['required', 'exists:users,id'],
        ]);

        $atelier = Atelier::create($validated);
       
        return $this->index();
    }

    public function update(Request $request, $id) {
        $atelier = Atelier::find($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('ateliers', 'name')->ignore($atelier->id),],
            'room' => ['required', 'string', 'max:255', Rule::unique('ateliers', 'room')->ignore($atelier->id), 'regex:/^([A-Z]{1}[0-9]{3})$/'],
            'manager_id' => ['required', 'exists:users,id'],
        ]);

        $atelier->update($validated);


        return $this->index();
    }

    public function dashboard($id)
    {
        $atelier = Atelier::with(['users.roles'])->findOrFail($id);
    
        $teachers = $atelier->users->filter(function ($user) {
            return $user->pivot->teacher == true;
        });
    
        $users = $atelier->users->filter(function ($user) {
            return $user->pivot->teacher == false;
        });

        $equipments = EquipmentResource::collection($atelier->equipments);  
    
        return inertia('Atelier/Dashboard', [
            'atelier' => $atelier,
            'users' => $users->values(), // Ensure collection is re-indexed
            'teachers' => $teachers->values(), // Ensure collection is re-indexed
            'equipments' => $equipments,
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
        $availableUsers = User::whereNotIn('id', $usersInAtelier)
        ->whereHas('roles', function($query) {
            $query->where('name', '!=', 'admin');
        })
        ->where('id', '!=', $atelier->manager_id)
        ->get();
    
        return response()->json($availableUsers);
    }

    public function usersInAtelier($atelierId){
        $atelier = Atelier::findOrFail($atelierId);
        $usersInAtelier = $atelier->users()->wherePivot('teacher', 0)->select('users.id', 'name')->get();
        
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
        $atelier->users()->updateExistingPivot($userId, ['teacher' => false]);
    
        return response()->json(['message' => 'Teacher role removed successfully.', 'user' => $user]);
    }

    public function addTeachers(Request $request, $atelierId) {
        
        $atelier = Atelier::findOrFail($atelierId);
        $userIds = $request->input('users');

        foreach ($userIds as $userId) {
            
            // Check if the user is already attached to avoid duplicates
            if (!$atelier->users()->where('users.id', $userId)->exists()) {
                $atelier->users()->attach($userId);
            }
            $atelier->users()->updateExistingPivot($userId, ['teacher' => true]);
        }

        $newTeachers = $atelier->users()->whereIn('users.id', $userIds)->wherePivot('teacher', true)->get();

        return response()->json(['message' => 'Teachers added successfully.', 'teachers' => $newTeachers]);
    
    }

    public function getEquipment($atelierId)
    {
        $atelier = Atelier::findOrFail($atelierId);
        $equipments = EquipmentResource::collection($atelier->equipments->where('can_be_borrowed', true));
        return $equipments;
    }

    public function addRestrictions(Request $request, $atelierId) {
        $equipmentIds = collect($request->input('equipments'))->pluck('id');
        $userId = $request->input('user_id');
        $user = User::find($userId);

        $user->restrictions()->attach($equipmentIds);

        return response()->json(['message' => $equipmentIds]);
    }

    public function removeRestrictions(Request $request, $atelierId) {
        $equipmentIds = collect($request->input('equipments'))->pluck('id');
        $userId = $request->input('user_id');
        $user = User::find($userId);

        $user->restrictions()->detach($equipmentIds);

        return response()->json(['message' => $equipmentIds]);
    }

    public function getRestrictedEquipment($atelierId, $userId)
    {
        try {
            $atelier = Atelier::findOrFail($atelierId);
            $restrictedEquipment = $atelier->equipments()->whereHas('restrictions', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })->get();
    
            return response()->json(['data' => $restrictedEquipment]);
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Failed to fetch restricted equipment', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to fetch restricted equipment'], 500);
        }
    }


}
