<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Handles User CRUD operations
 * 
 */
class UserController extends Controller
{

    /**
     * Get a listing of users.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $data = User::all();

        return response()->json([
            'status'  => true,
            'message' => 'OK.',
            'data'    => $data,
            'error'   => null,
        ]);
    }

    /**
     * Get the specified resource.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $user = User::find($id);

        if(!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'No such user.',
                'data'    => $user,
                'error'   => null,
            ], 404);
        }
        
        return response()->json([
            'status'  => true,
            'message' => 'OK.',
            'data'    => $user,
            'error'   => null,
        ]);
    }

    /**
     * Update the specified user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        $user = User::find($id);

        if(!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'No such user.',
                'data'    => $user,
                'error'   => null,
            ], 404);
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update($validatedData);
        
        return response()->json([
            'status'  => true,
            'message' => 'User updated.',
            'data'    => $user,
            'error'   => null,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        $user = User::find($id);

        if(!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'No such user.',
                'data'    => $user,
                'error'   => null,
            ], 404);
        }

        $user->destroy();
        
        return response()->json([
            'status'  => true,
            'message' => 'User deleted.',
            'data'    => null,
            'error'   => null,
        ]);
    }
}
