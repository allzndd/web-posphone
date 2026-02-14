<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $permission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $permission)
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect('login');
        }

        // Check if user has permission
        if (!auth()->user()->hasPermission($permission)) {
            abort(403, 'Unauthorized - Permission not granted');
        }

        return $next($request);
    }
}
