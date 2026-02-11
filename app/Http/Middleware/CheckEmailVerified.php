<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckEmailVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Check if user is authenticated and email is not verified
        if ($user && !$user->hasVerifiedEmail()) {
            // Redirect to verification notice WITHOUT logging out
            // User needs to stay logged in to verify their email
            return redirect()->route('verification.notice');
        }

        return $next($request);
    }
}
