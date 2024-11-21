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
        $user = auth()->user();

        if (!$user->hasRole('admin') && !$user->isManager()) {
            return redirect()->route('dashboard')->with('error', 'You are not authorized to view this page');
        }

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
        } else {
            $ateliers = AtelierResource::collection(
                Atelier::where('manager_id', $user->id)->with(['manager', 'users'])->get()
            );
            return inertia('Atelier/Index', ['ateliers' => $ateliers, 'users' => $users, 'role' => 'manager']);
        }
    }

    public function create() {
        $user = auth()->user();
        if (!$user->hasRole('admin')) {
            return redirect()->route('dashboard')->with('error', 'You are not authorized to view this page');
        }

        $users = UserResource::collection(User::whereHas('roles', function($query) {
            $query->where('name', 'manager');
        })->get());

        return inertia('Atelier/Create', ['users' => $users]);
    }

    public function store(Request $request) {
        $user = auth()->user();
        if (!$user->hasRole('admin')) {
            return redirect()->route('dashboard')->with('error', 'You are not authorized to view this page');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'room' => ['required', 'string', 'max:255'],
            'manager_id' => ['required', 'exists:users,id'],
        ]);

        $atelier = Atelier::create($validated);
       
        return $this->index();
    }

    public function dashboard($id)
    {
        $atelier = Atelier::with(['users.roles'])->findOrFail($id);
    
        $teachers = $atelier->users->filter(function ($user) {
            return $user->hasRole('teacher');
        });
    
        $users = $atelier->users->filter(function ($user) {
            return $user->hasRole('user') && !$user->hasRole('teacher');
        });
    
        return inertia('Atelier/Dashboard', [
            'atelier' => $atelier,
            'users' => $users->values(), // Ensure collection is re-indexed
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
        $usersInAtelier = $atelier->users()
            ->whereDoesntHave('roles', function($query) {
                $query->where('name', 'teacher');
            })
            ->select('users.id', 'name')
            ->get();
        
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
        ]);

        $atelier->update($validated);


        return $this->index();
    }
    public function addTeachers(Request $request, $atelierId) {
        try {
            $atelier = Atelier::findOrFail($atelierId);
            $userIds = $request->input('users');
    
            foreach ($userIds as $userId) {
                $user = User::findOrFail($userId);
                // Check if the user is already attached to avoid duplicates
                if (!$atelier->users()->where('users.id', $userId)->exists()) {
                    $atelier->users()->attach($userId);
                }
                $user->assignRole('teacher');
            }
    
            $newTeachers = $atelier->users()->whereIn('users.id', $userIds)->get();
    
            return response()->json(['message' => 'Teachers added successfully.', 'users' => $newTeachers]);
        } catch (\Exception $e) {
            \Log::error('Failed to add teachers to atelier: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to add teachers to atelier'], 500);
        }
    }

}
