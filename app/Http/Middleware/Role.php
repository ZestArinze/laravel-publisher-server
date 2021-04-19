<?php

namespace App\Http\Middleware;

use App\Models\Role as UserRole;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Role
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $rolesDotSeparated)
    {
        // check that the user is authenticated
        if (!auth()->check()) {
            return response()->json([
                'status'  => false,
                'message' => 'Access denied.',
                'data'    => null,
                'error'   => null,
            ], 401);
        }       

        // extract the role required to access this route
        $roleNames = explode(".", $rolesDotSeparated);

        // allow request to proceed if user role matches any of the allowed roles
        foreach ($roleNames as $roleName) {

            if (!isset(UserRole::ALL[$roleName])) {
                continue;
            }

            if ($request->user()->role === $roleName) {
                return $next($request);
            }
        }
    
        // user does not have the role
        return response()->json([
            'status'  => false,
            'message' => 'Unauthorized.',
            'data'    => null,
            'error'   => null,
        ], 401);

    }
}
