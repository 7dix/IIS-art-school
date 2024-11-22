<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Http\Resources\RoleResource;
use App\Models\User;
use App\Models\Role;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct() {
        parent::__construct();

        $this->middleware(function ($request, $next) {
            if (!$this->user || !$this->user->can('manage_user')) {
                return redirect()->route('dashboard'); // Unauthorized access
            }

            return $next($request);
        });

    }



    public function index() {
       
        $users = UserResource::collection(User::all());
        $roles = RoleResource::collection(Role::all());
        return inertia('User/Index', ['users' => $users, 'roles' => $roles]);
    }


    public function update(Request $request, $id) {

        $user = User::find($id);
        $user->update($request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
        ]));
        
        if ($user->getRoleNames()[0] != $request->role) {
            $user->removeRole($user->getRoleNames()[0]);
            $user->assignRole($request->role);
        }

        return $this->index();
    }
}