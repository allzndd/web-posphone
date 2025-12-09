<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LayananController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $layanan = Layanan::where('owner_id', $ownerId)
            ->when($request->input('nama'), function ($query, $nama) {
                return $query->where('nama', 'like', '%' . $nama . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 10));

        return view('layanan.index', compact('layanan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('layanan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $request->validate([
            'nama' => 'required|string|max:45',
            'keterangan' => 'nullable|string|max:45',
            'harga' => 'required|numeric|min:0',
            'durasi' => 'nullable|integer',
            'pos_toko_id' => 'nullable|exists:pos_toko,id',
            'pos_pelanggan_id' => 'nullable|exists:pos_pelanggan,id',
        ]);

        Layanan::create([
            'owner_id' => $ownerId,
            'pos_toko_id' => $request->pos_toko_id,
            'pos_pelanggan_id' => $request->pos_pelanggan_id,
            'nama' => $request->nama,
            'keterangan' => $request->keterangan,
            'harga' => $request->harga,
            'durasi' => $request->durasi,
        ]);

        return redirect()->route('layanan.index')->with('success', 'Service successfully created');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $item = Layanan::where('owner_id', $ownerId)
            ->where('id', $id)
            ->firstOrFail();

        return view('layanan.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $item = Layanan::where('owner_id', $ownerId)
            ->where('id', $id)
            ->firstOrFail();

        return view('layanan.edit', compact('item'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $item = Layanan::where('owner_id', $ownerId)
            ->where('id', $id)
            ->firstOrFail();

        $request->validate([
            'nama' => 'required|string|max:45',
            'keterangan' => 'nullable|string|max:45',
            'harga' => 'required|numeric|min:0',
            'durasi' => 'nullable|integer',
            'pos_toko_id' => 'nullable|exists:pos_toko,id',
            'pos_pelanggan_id' => 'nullable|exists:pos_pelanggan,id',
        ]);

        $item->update([
            'pos_toko_id' => $request->pos_toko_id,
            'pos_pelanggan_id' => $request->pos_pelanggan_id,
            'nama' => $request->nama,
            'keterangan' => $request->keterangan,
            'harga' => $request->harga,
            'durasi' => $request->durasi,
        ]);

        return redirect()->route('layanan.index')->with('success', 'Service successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $item = Layanan::where('owner_id', $ownerId)
            ->where('id', $id)
            ->firstOrFail();

        $item->delete();

        return redirect()->route('layanan.index')->with('success', 'Service successfully deleted');
    }
}
