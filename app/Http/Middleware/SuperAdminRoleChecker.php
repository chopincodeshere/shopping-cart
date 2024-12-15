<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminRoleChecker
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $user = Auth::user();

        if ($user->role !== User::SUPER_ADMIN_ROLE) {
            return response()->json([
                'message' => 'Unauthenticated. You are not a super admin.',
                'status' => '0',

            ], 401);
        }
        
        return $next($request);
    }
}
