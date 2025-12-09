<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Owner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class KelolaOwnerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get all users with role_id = 2 (Owner)
        $owners = User::where('role_id', 2)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('kelola-owner.index', compact('owners'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('kelola-owner.create');
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
        Owner::create([
            'pengguna_id' => $user->id,
        ]);

        return redirect()->route('kelola-owner.index')->with('success', 'Owner berhasil ditambahkan');
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
        $owner = User::where('role_id', 2)->findOrFail($id);
        return view('kelola-owner.edit', compact('owner'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $owner = User::where('role_id', 2)->findOrFail($id);
        
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:pengguna,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
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
