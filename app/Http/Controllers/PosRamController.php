<?php

namespace App\Http\Controllers;

use App\Models\PosRam;
use App\Models\User;
use Illuminate\Http\Request;

class PosRamController extends Controller
{
    /**
     * Display a listing of the ram.
     */
    public function index()
    {
        $posRams = PosRam::with('owner')->paginate(15);
        return view('pages.pos-ram.index', compact('posRams'));
    }

    /**
     * Show the form for creating a new ram.
     */
    public function create()
    {
        $owners = User::where('role_id', 2)->get(); // role_id 2 = owner
        return view('pages.pos-ram.create', compact('owners'));
    }

    /**
     * Store a newly created ram in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_owner' => 'nullable|integer|exists:users,id',
            'kapasitas' => 'required|integer|min:0',
            'is_global' => 'nullable|integer',
        ]);

        // For superadmin, set id_owner to null and is_global to 1
        if (auth()->user()->role_id === 1) {
            $validated['id_owner'] = null;
            $validated['is_global'] = 1;
        }

        PosRam::create($validated);

        return redirect()->route('pos-ram.index')
            ->with('success', 'RAM berhasil ditambahkan');
    }

    /**
     * Display the specified ram.
     */
    public function show(PosRam $posRam)
    {
        return view('pages.pos-ram.show', compact('posRam'));
    }

    /**
     * Show the form for editing the specified ram.
     */
    public function edit(PosRam $posRam)
    {
        $owners = User::where('role_id', 2)->get(); // role_id 2 = owner
        return view('pages.pos-ram.edit', compact('posRam', 'owners'));
    }

    /**
     * Update the specified ram in storage.
     */
    public function update(Request $request, PosRam $posRam)
    {
        $validated = $request->validate([
            'id_owner' => 'nullable|integer|exists:users,id',
            'kapasitas' => 'required|integer|min:0',
            'is_global' => 'nullable|integer',
        ]);

        // For superadmin, set id_owner to null and is_global to 1
        if (auth()->user()->role_id === 1) {
            $validated['id_owner'] = null;
            $validated['is_global'] = 1;
        }

        $posRam->update($validated);

        return redirect()->route('pos-ram.index')
            ->with('success', 'RAM berhasil diperbarui');
    }

    /**
     * Remove the specified ram from storage.
     */
    public function destroy(PosRam $posRam)
    {
        $posRam->delete();

        return redirect()->route('pos-ram.index')
            ->with('success', 'RAM berhasil dihapus');
    }

    /**
     * Delete multiple ram items
     */
    public function bulkDestroy(Request $request)
    {
        $ids = json_decode($request->input('ids'), true);
        
        if (!is_array($ids) || empty($ids)) {
            return redirect()->route('pos-ram.index')
                ->with('error', 'Tidak ada data yang dipilih');
        }

        PosRam::whereIn('id', $ids)->delete();

        return redirect()->route('pos-ram.index')
            ->with('success', 'RAM berhasil dihapus');
    }
}
