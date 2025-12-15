<?php

namespace App\Http\Controllers;

use App\Models\PosToko;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TokoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $tokos = PosToko::where('owner_id', $ownerId)
            ->when($request->input('nama'), function ($query, $nama) {
                return $query->where('nama', 'like', '%' . $nama . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 10));

        return view('pages.toko.index', compact('tokos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.toko.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
        ]);

        PosToko::create([
            'owner_id' => $ownerId,
            'nama' => $request->nama,
            'alamat' => $request->alamat,
        ]);

        return redirect()->route('toko.index')->with('success', 'Toko berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $toko = PosToko::where('owner_id', $ownerId)
            ->where('id', $id)
            ->with(['pengguna.role'])
            ->firstOrFail();

        return view('pages.toko.show', compact('toko'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PosToko $toko)
    {
        return view('pages.toko.edit', compact('toko'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PosToko $toko)
    {
        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $toko = PosToko::where('owner_id', $ownerId)
            ->where('id', $id)
            ->firstOrFail();

        $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
        ]);

        $toko->update([
            'nama' => $request->nama,
            'alamat' => $request->alamat,
        ]);

        return redirect()->route('toko.index')->with('success', 'Toko berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PosToko $toko)
    {
        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $toko = PosToko::where('owner_id', $ownerId)
            ->where('id', $id)
            ->firstOrFail();

        $toko->delete();

        return redirect()->route('toko.index')->with('success', 'Toko berhasil dihapus');
    }
}
