<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Http\Resources\RoleResource;
use App\Models\User;
use App\Models\Role;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = UserResource::collection(
            User::with('role')->get()
        );
        $roles = RoleResource::collection(Role::all());
        return inertia('User/Index', ['users' => $users, 'roles' => $roles]);
    }


    public function update(Request $request, $id) {
        $user = User::find($id);
        $user->update($request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'role_id' => ['required', 'exists:roles,id'],
        ]));

        return inertia('User/Index', ['users' => UserResource::collection(User::all()), 
            'roles' => RoleResource::collection(Role::all())]);
    }
}