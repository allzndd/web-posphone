<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Owner;
use App\Models\Langganan;
use Carbon\Carbon;

class CheckSubscriptionStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Skip check for super admin (role_id = 1) or if no user is authenticated
        if (!$user || $user->role_id == 1) {
            return $next($request);
        }

        // Get owner record
        $owner = Owner::where('pengguna_id', $user->id)->first();

        if (!$owner) {
            return redirect()->route('login')->with('error', 'Owner account not found.');
        }

        // Get active subscription
        $subscription = Langganan::where('owner_id', $owner->id)
            ->orderBy('created_at', 'desc')
            ->first();

        // If no subscription exists
        if (!$subscription) {
            return redirect()->route('pembayaran.expired')
                ->with('error', 'No active subscription found. Please subscribe to continue.');
        }

        // Check if subscription is expired
        $now = Carbon::now();
        if ($subscription->end_date && Carbon::parse($subscription->end_date)->lt($now)) {
            // Auto-downgrade to free tier instead of blocking
            $isDowngraded = $subscription->downgradeToFreeTier();
            
            if ($isDowngraded) {
                return redirect()->route('dashboard')
                    ->with('warning', 'Your subscription has expired. You have been downgraded to Free Tier. Upgrade anytime to unlock more features.');
            } else {
                return redirect()->route('pembayaran.expired')
                    ->with('error', 'Your subscription has expired. Please contact support.');
            }
        }

        // If subscription is inactive but not trial, allow free tier access
        if ($subscription->is_active == 0 && $subscription->is_trial == 0) {
            // Already on free tier, allow access
            return $next($request);
        }

        // Subscription is valid (either trial, paid, or free tier)
        return $next($request);
    }
}
