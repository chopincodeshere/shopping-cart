<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserRoleChecker
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user->role !== User::USER_ROLE && $user->role !== User::ADMIN_ROLE && $user->role !== User::SUPER_ADMIN_ROLE) {
            return response()->json([
                'message' => 'Unauthenticated. You are not a user.',
                'status' => '0',

            ], 401);
        }

        return $next($request);
    }
}
