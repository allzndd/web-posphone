<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pembayaran;
use App\Models\User;

class PembayaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pembayaran = Pembayaran::with('owner')
            ->orderBy('tanggal', 'desc')
            ->get();
        
        return view('pembayaran.index', compact('pembayaran'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get all owners (users with role OWNER)
        $owners = User::where('roles', 'OWNER')->get();
        
        return view('pembayaran.create', compact('owners'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'owner_id' => 'required|exists:users,id',
            'paket' => 'required|string|max:255',
            'periode' => 'required|string|max:255',
            'total' => 'required|numeric|min:0',
            'status' => 'required|in:Paid,Pending,Failed',
            'notes' => 'nullable|string',
        ]);

        // Get owner details
        $owner = User::findOrFail($validated['owner_id']);
        
        $validated['owner_name'] = $owner->name;
        $validated['email'] = $owner->email;

        Pembayaran::create($validated);

        return redirect()->route('pembayaran.index')
            ->with('success', 'Payment successfully added');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $item = Pembayaran::with('owner')->findOrFail($id);
        
        return view('pembayaran.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $item = Pembayaran::findOrFail($id);
        $owners = User::where('roles', 'OWNER')->get();
        
        return view('pembayaran.edit', compact('item', 'owners'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $pembayaran = Pembayaran::findOrFail($id);

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'owner_id' => 'required|exists:users,id',
            'paket' => 'required|string|max:255',
            'periode' => 'required|string|max:255',
            'total' => 'required|numeric|min:0',
            'status' => 'required|in:Paid,Pending,Failed',
            'notes' => 'nullable|string',
        ]);

        // Get owner details
        $owner = User::findOrFail($validated['owner_id']);
        
        $validated['owner_name'] = $owner->name;
        $validated['email'] = $owner->email;

        $pembayaran->update($validated);

        return redirect()->route('pembayaran.index')
            ->with('success', 'Payment successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pembayaran = Pembayaran::findOrFail($id);
        $pembayaran->delete();

        return redirect()->route('pembayaran.index')
            ->with('success', 'Payment successfully deleted');
    }
}
