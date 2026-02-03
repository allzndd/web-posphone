<?php

namespace App\Http\Controllers;

use App\Models\PosPenyimpanan;
use App\Models\User;
use Illuminate\Http\Request;

class PosPenyimpananController extends Controller
{
    /**
     * Display a listing of the penyimpanan.
     */
    public function index()
    {
        $posPenyimpanans = PosPenyimpanan::with('owner')->paginate(15);
        return view('pages.pos-penyimpanan.index', compact('posPenyimpanans'));
    }

    /**
     * Show the form for creating a new penyimpanan.
     */
    public function create()
    {
        $owners = User::where('role_id', 2)->get(); // role_id 2 = owner
        return view('pages.pos-penyimpanan.create', compact('owners'));
    }

    /**
     * Store a newly created penyimpanan in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_owner' => 'required|exists:users,id',
            'kapasitas' => 'required|integer|min:0',
            'id_global' => 'nullable|integer',
        ]);

        PosPenyimpanan::create($validated);

        return redirect()->route('pos-penyimpanan.index')
            ->with('success', 'Penyimpanan berhasil ditambahkan');
    }

    /**
     * Display the specified penyimpanan.
     */
    public function show(PosPenyimpanan $posPenyimpanan)
    {
        return view('pages.pos-penyimpanan.show', compact('posPenyimpanan'));
    }

    /**
     * Show the form for editing the specified penyimpanan.
     */
    public function edit(PosPenyimpanan $posPenyimpanan)
    {
        $owners = User::where('role_id', 2)->get(); // role_id 2 = owner
        return view('pages.pos-penyimpanan.edit', compact('posPenyimpanan', 'owners'));
    }

    /**
     * Update the specified penyimpanan in storage.
     */
    public function update(Request $request, PosPenyimpanan $posPenyimpanan)
    {
        $validated = $request->validate([
            'id_owner' => 'required|exists:users,id',
            'kapasitas' => 'required|integer|min:0',
            'id_global' => 'nullable|integer',
        ]);

        $posPenyimpanan->update($validated);

        return redirect()->route('pos-penyimpanan.index')
            ->with('success', 'Penyimpanan berhasil diperbarui');
    }

    /**
     * Remove the specified penyimpanan from storage.
     */
    public function destroy(PosPenyimpanan $posPenyimpanan)
    {
        $posPenyimpanan->delete();

        return redirect()->route('pos-penyimpanan.index')
            ->with('success', 'Penyimpanan berhasil dihapus');
    }

    /**
     * Delete multiple penyimpanan items
     */
    public function bulkDestroy(Request $request)
    {
        $ids = json_decode($request->input('ids'), true);
        
        if (!is_array($ids) || empty($ids)) {
            return redirect()->route('pos-penyimpanan.index')
                ->with('error', 'Tidak ada data yang dipilih');
        }

        PosPenyimpanan::whereIn('id', $ids)->delete();

        return redirect()->route('pos-penyimpanan.index')
            ->with('success', 'Penyimpanan berhasil dihapus');
    }
}
