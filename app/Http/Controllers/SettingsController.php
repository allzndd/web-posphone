<?php

namespace App\Http\Controllers;

use App\Models\OwnerSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
                return redirect()->route('settings.index')
                    ->with('error', 'Cannot change currency when you have existing products, services, or transactions. This would cause price inconsistencies.');
            }
        }

        $settings->update($validated);

        return redirect()->route('settings.index')
            ->with('success', 'Settings updated successfully');
    }
}
