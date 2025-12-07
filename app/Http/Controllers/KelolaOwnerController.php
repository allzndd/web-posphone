<?php

namespace App\Http\Controllers;

use App\Models\KelolaOwner;
use Illuminate\Http\Request;

class KelolaOwnerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $owners = KelolaOwner::latest()->get();
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
            'nama_perusahaan' => 'required|string|max:255',
            'nama_pemilik' => 'required|string|max:255',
            'email' => 'required|email|unique:kelola_owner,email',
            'telepon' => 'required|string|max:20',
            'paket' => 'required|string',
            'jumlah_outlet' => 'required|integer|min:1',
            'tanggal_daftar' => 'required|date',
            'tanggal_expired' => 'required|date|after:tanggal_daftar',
        ]);

        // Auto-set status based on expiration date
        $validated['status'] = now() > $validated['tanggal_expired'] ? 'Expired' : 'Active';

        KelolaOwner::create($validated);

        return redirect()->route('kelola-owner.index')->with('success', 'Owner successfully created');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $owner = KelolaOwner::findOrFail($id);
        return view('kelola-owner.show', compact('owner'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $owner = KelolaOwner::findOrFail($id);
        return view('kelola-owner.edit', compact('owner'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $owner = KelolaOwner::findOrFail($id);
        
        $validated = $request->validate([
            'nama_perusahaan' => 'required|string|max:255',
            'nama_pemilik' => 'required|string|max:255',
            'email' => 'required|email|unique:kelola_owner,email,' . $id,
            'telepon' => 'required|string|max:20',
            'paket' => 'required|string',
            'jumlah_outlet' => 'required|integer|min:1',
            'tanggal_daftar' => 'required|date',
            'tanggal_expired' => 'required|date|after:tanggal_daftar',
            'status' => 'required|in:Active,Expired',
        ]);

        $owner->update($validated);

        return redirect()->route('kelola-owner.index')->with('success', 'Owner successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $owner = KelolaOwner::findOrFail($id);
        $owner->delete();

        return redirect()->route('kelola-owner.index')->with('success', 'Owner successfully deleted');
    }
}
