<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  ...$roles
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // Map role names to methods
        $roleChecks = [
            'SUPERADMIN' => 'isSuperadmin',
            'OWNER' => 'isOwner',
            'ADMIN' => 'isAdmin',
        ];

        // Check if user has any of the required roles
        foreach ($roles as $role) {
            if (isset($roleChecks[$role]) && method_exists($user, $roleChecks[$role])) {
                if ($user->{$roleChecks[$role]}()) {
                    return $next($request);
                }
            }
        }

        abort(403, 'Unauthorized action.');
    }
}
