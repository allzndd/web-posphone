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
        $pembayaran = Pembayaran::with(['owner.pengguna', 'langganan.tipeLayanan'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pembayaran.index', compact('pembayaran'));
    }    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get all owners
        $owners = \App\Models\Owner::with('pengguna')->get();

        return view('pembayaran.create', compact('owners'));
    }    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'owner_id' => 'required|exists:owner,id',
            'langganan_id' => 'required|exists:langganan,id',
            'nominal' => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|string|max:100',
            'status' => 'required|in:Paid,Pending,Failed',
        ]);

        $validated['paid_at'] = $request->status === 'Paid' ? now() : null;
        $validated['created_at'] = now();

        Pembayaran::create($validated);

        return redirect()->route('pembayaran.index')
            ->with('success', 'Payment successfully added');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $item = Pembayaran::with(['owner.pengguna', 'langganan.tipeLayanan'])->findOrFail($id);
        
        return view('pembayaran.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $item = Pembayaran::with(['owner.pengguna', 'langganan'])->findOrFail($id);
        $owners = \App\Models\Owner::with('pengguna')->get();
        
        return view('pembayaran.edit', compact('item', 'owners'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $pembayaran = Pembayaran::findOrFail($id);

        $validated = $request->validate([
            'owner_id' => 'required|exists:owner,id',
            'langganan_id' => 'required|exists:langganan,id',
            'nominal' => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|string|max:100',
            'status' => 'required|in:Paid,Pending,Failed',
        ]);

        // Update paid_at based on status
        if ($request->status === 'Paid' && !$pembayaran->paid_at) {
            $validated['paid_at'] = now();
        } elseif ($request->status !== 'Paid') {
            $validated['paid_at'] = null;
        }

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
