<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
     //--------------------------------------------------
    // leaving out: forgot password, change password,
    // change user role, email confirmation, etc.
    //--------------------------------------------------

    /**
     * Store a newly created user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return JsonResponse
     */
    public function createUser(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|confirmed|min:8',
            'password_confirmation' => 'required|string',
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role = Role::USER;
        $user->save(); 
        
        return response()->json([
            'status'  => true,
            'message' => 'Account created. You may login now.',
            'data'    => $user,
            'error'   => null,
        ], 201);
    }

    /**
     * Handle user authentication
     *
     * @param  \Illuminate\Http\Request  $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'status'  => false,
                'message' => 'Incorrect login credentials.',
                'data'    => null,
                'error'   => null,
            ], 401);
        }

        // create new auth token
        $token = $request->user()->createToken('Personal Access Token');
        
        return response()->json([
            'status'  => true,
            'message' => 'Login successful.',
            'data'    => [
                'auth_token' => $token->plainTextToken,
                'token_type' => 'Bearer token',
            ],
            'error'   => null,
        ]);
    }
}
