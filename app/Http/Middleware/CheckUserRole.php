<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckUserRole
{
    public function handle($request, Closure $next)
    {
        // Check if the user is authenticated
        if ($user = Auth::user()) {
            // Store the user's role information in the request
            session()->save('user_role', $user->role);
        }


        return $next($request);
    }
}
