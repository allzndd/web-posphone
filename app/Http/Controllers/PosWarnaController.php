<?php

namespace App\Http\Controllers;

use App\Models\PosWarna;
use App\Models\User;
use Illuminate\Http\Request;

class PosWarnaController extends Controller
{
    /**
     * Display a listing of the warna.
     */
    public function index()
    {
        $posWarnas = PosWarna::with('owner')->paginate(15);
        return view('pages.pos-warna.index', compact('posWarnas'));
    }

    /**
     * Show the form for creating a new warna.
     */
    public function create()
    {
        $owners = User::where('role_id', 2)->get(); // role_id 2 = owner
        return view('pages.pos-warna.create', compact('owners'));
    }

    /**
     * Store a newly created warna in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_owner' => 'nullable|exists:users,id',
            'warna' => 'required|string|max:100',
            'is_global' => 'nullable|integer',
        ]);

        PosWarna::create($validated);

        return redirect()->route('pos-warna.index')
            ->with('success', 'Warna berhasil ditambahkan');
    }

    /**
     * Display the specified warna.
     */
    public function show(PosWarna $posWarna)
    {
        return view('pages.pos-warna.show', compact('posWarna'));
    }

    /**
     * Show the form for editing the specified warna.
     */
    public function edit(PosWarna $posWarna)
    {
        $owners = User::where('role_id', 2)->get(); // role_id 2 = owner
        return view('pages.pos-warna.edit', compact('posWarna', 'owners'));
    }

    /**
     * Update the specified warna in storage.
     */
    public function update(Request $request, PosWarna $posWarna)
    {
        $validated = $request->validate([
            'id_owner' => 'nullable|exists:users,id',
            'warna' => 'required|string|max:100',
            'is_global' => 'nullable|integer',
        ]);

        $posWarna->update($validated);

        return redirect()->route('pos-warna.index')
            ->with('success', 'Warna berhasil diperbarui');
    }

    /**
     * Remove the specified warna from storage.
     */
    public function destroy(PosWarna $posWarna)
    {
        $posWarna->delete();

        return redirect()->route('pos-warna.index')
            ->with('success', 'Warna berhasil dihapus');
    }

    /**
     * Delete multiple warna items
     */
    public function bulkDestroy(Request $request)
    {
        $ids = json_decode($request->input('ids'), true);
        
        if (!is_array($ids) || empty($ids)) {
            return redirect()->route('pos-warna.index')
                ->with('error', 'Tidak ada data yang dipilih');
        }

        PosWarna::whereIn('id', $ids)->delete();

        return redirect()->route('pos-warna.index')
            ->with('success', 'Warna berhasil dihapus');
    }
}
