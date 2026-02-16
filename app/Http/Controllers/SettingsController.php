<?php

namespace App\Http\Controllers;

use App\Models\OwnerSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SettingsController extends Controller
{
    /**
     * Show settings page
     */
    public function index()
    {
        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        // Get or create settings
        $settings = OwnerSetting::firstOrCreate(
            ['owner_id' => $ownerId],
            ['currency' => 'IDR']
        );

        // Check if owner has existing data
        $hasProducts = \App\Models\PosProduk::where('owner_id', $ownerId)->exists();
        $hasTransactions = \App\Models\PosTransaksi::where('owner_id', $ownerId)->exists();
        $hasServices = \App\Models\PosService::where('owner_id', $ownerId)->exists();
        
        $hasData = $hasProducts || $hasTransactions || $hasServices;

        return view('pages.settings.index', compact('settings', 'hasData'));
    }

    /**
     * Update settings
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        // Validate based on which tab is being submitted
        $activeTab = $request->input('active_tab', 'profile');

        if ($activeTab === 'profile') {
            return $this->updateProfile($request, $user);
        } else {
            return $this->updateFinance($request, $ownerId);
        }
    }

    /**
     * Update profile information
     */
    private function updateProfile(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:pengguna,email,' . $user->id,
            'current_password' => 'nullable|required_with:password',
            'password' => 'nullable|min:8|confirmed',
        ]);

        // Handle name update
        if ($validated['name'] !== $user->nama) {
            $user->update(['nama' => $validated['name']]);
        }

        // Handle password update
        if (!empty($validated['password'])) {
            // Verify current password
            if (!Hash::check($validated['current_password'], $user->password)) {
                return redirect()->route('settings.index', ['tab' => 'profile'])
                    ->withErrors(['current_password' => 'Current password is incorrect.']);
            }

            $user->update(['password' => Hash::make($validated['password'])]);
        }

        // Handle email update
        if (!empty($validated['email']) && $validated['email'] !== $user->email) {
            // Check if email is already verified
            if ($user->email_is_verified == 1) {
                // Require verification for new email
                $verificationToken = Str::random(64);
                
                // Store pending email in session
                $request->session()->put('pending_email', [
                    'email' => $validated['email'],
                    'token' => $verificationToken,
                    'created_at' => now()
                ]);

                // Send verification email
                $this->sendEmailVerification($user, $validated['email'], $verificationToken);

                return redirect()->route('settings.index', ['tab' => 'profile'])
                    ->with('success', 'Verification link sent to your new email. Please verify it to complete the email change.');
            }
        }

        return redirect()->route('settings.index', ['tab' => 'profile'])
            ->with('success', 'Profile updated successfully');
    }

    /**
     * Update finance settings
     */
    private function updateFinance(Request $request, $ownerId)
    {
        $validated = $request->validate([
            'currency' => 'required|in:IDR,MYR,USD',
        ]);

        $settings = OwnerSetting::firstOrCreate(['owner_id' => $ownerId]);
        
        // Check if currency is being changed and if there's existing data
        if ($settings->currency !== $validated['currency']) {
            $hasProducts = \App\Models\PosProduk::where('owner_id', $ownerId)->exists();
            $hasTransactions = \App\Models\PosTransaksi::where('owner_id', $ownerId)->exists();
            $hasServices = \App\Models\PosService::where('owner_id', $ownerId)->exists();
            
            if ($hasProducts || $hasTransactions || $hasServices) {
                return redirect()->route('settings.index', ['tab' => 'finance'])
                    ->with('error', 'Cannot change currency when you have existing products, services, or transactions.');
            }
        }

        $settings->update($validated);

        return redirect()->route('settings.index', ['tab' => 'finance'])
            ->with('success', 'Finance settings updated successfully');
    }

    /**
     * Send email verification
     */
    private function sendEmailVerification($user, $newEmail, $token)
    {
        // You can implement actual email sending here using Laravel's Mail facade
        // For now, we'll just log the token
        // In production, send an actual email with verification link
        
        $verificationUrl = route('email.verify', ['token' => $token]);
        
        // Log or send email
        \Log::info("Email verification for {$newEmail}: {$verificationUrl}");
    }

    /**
     * Verify new email
     */
    public function verifyNewEmail(Request $request, $token)
    {
        $user = Auth::user();
        $pendingEmail = $request->session()->get('pending_email');

        if (!$pendingEmail || $pendingEmail['token'] !== $token) {
            return redirect()->route('settings.index', ['tab' => 'profile'])
                ->with('error', 'Invalid or expired verification token.');
        }

        // Check if token is not older than 24 hours
        if (now()->diffInHours($pendingEmail['created_at']) > 24) {
            $request->session()->forget('pending_email');
            return redirect()->route('settings.index', ['tab' => 'profile'])
                ->with('error', 'Verification link has expired. Please request a new one.');
        }

        // Update email and mark as verified
        $user->update([
            'email' => $pendingEmail['email'],
            'email_is_verified' => 1
        ]);

        $request->session()->forget('pending_email');

        return redirect()->route('settings.index', ['tab' => 'profile'])
            ->with('success', 'Email verified successfully!');
    }
}
