<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Owner;
use App\Models\TipeLayanan;
use App\Models\Langganan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class KelolaOwnerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get all users with role_id = 2 (Owner) with owner and langganan relationship
        $owners = User::where('role_id', 2)
            ->with(['owner'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('kelola-owner.index', compact('owners'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $packages = TipeLayanan::all();
        return view('kelola-owner.create', compact('packages'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:pengguna,email',
            'password' => 'required|string|min:8|confirmed',
            'tipe_layanan_id' => 'required|exists:tipe_layanan,id',
            'started_date' => 'required|date',
            'is_trial' => 'nullable|boolean',
        ]);

        // Create user with owner role
        $user = User::create([
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'slug' => Str::slug($validated['nama']),
            'role_id' => 2, // Owner role
            'email_is_verified' => 1,
        ]);

        // Create owner entry
        $owner = Owner::create([
            'pengguna_id' => $user->id,
        ]);

        // Get package duration
        $package = TipeLayanan::findOrFail($validated['tipe_layanan_id']);
        $startDate = Carbon::parse($validated['started_date']);
        $endDate = $startDate->copy()->addMonths($package->durasi);

        // Create langganan (subscription)
        Langganan::create([
            'owner_id' => $owner->id,
            'tipe_layanan_id' => $validated['tipe_layanan_id'],
            'started_date' => $startDate,
            'end_date' => $endDate,
            'is_trial' => $request->has('is_trial') ? 1 : 0,
            'is_active' => 1,
        ]);

        return redirect()->route('kelola-owner.index')->with('success', 'Owner berhasil ditambahkan dan langganan dibuat');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $owner = User::where('role_id', 2)->findOrFail($id);
        return view('kelola-owner.show', compact('owner'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $owner = User::where('role_id', 2)->with('owner')->findOrFail($id);
        $packages = TipeLayanan::all();
        
        // Get active subscription if exists
        $subscription = null;
        if ($owner->owner) {
            $subscription = Langganan::where('owner_id', $owner->owner->id)
                ->orderBy('created_at', 'desc')
                ->first();
        }
        
        return view('kelola-owner.edit', compact('owner', 'packages', 'subscription'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $owner = User::where('role_id', 2)->with('owner')->findOrFail($id);
        
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:pengguna,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'tipe_layanan_id' => 'nullable|exists:tipe_layanan,id',
            'started_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'is_trial' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $updateData = [
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'slug' => Str::slug($validated['nama']),
        ];

        // Only update password if provided
        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $owner->update($updateData);

        // Update subscription if package is selected
        if ($request->filled('tipe_layanan_id') && $owner->owner) {
            $subscription = Langganan::where('owner_id', $owner->owner->id)
                ->orderBy('created_at', 'desc')
                ->first();
            
            if ($subscription) {
                $subscription->update([
                    'tipe_layanan_id' => $validated['tipe_layanan_id'],
                    'started_date' => $request->started_date ?? $subscription->started_date,
                    'end_date' => $request->end_date ?? $subscription->end_date,
                    'is_trial' => $request->has('is_trial') ? 1 : 0,
                    'is_active' => $request->has('is_active') ? 1 : 0,
                ]);
            }
        }

        return redirect()->route('kelola-owner.index')->with('success', 'Owner berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $owner = User::where('role_id', 2)->findOrFail($id);
        
        // Delete owner entry first
        Owner::where('pengguna_id', $id)->delete();
        
        // Then delete user
        $owner->delete();

        return redirect()->route('kelola-owner.index')->with('success', 'Owner berhasil dihapus');
    }
}
