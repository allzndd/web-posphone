<?php

namespace App\Http\Controllers;

use App\Models\PosPelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PelangganController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $pelanggan = PosPelanggan::where('owner_id', $ownerId)
            ->when($request->input('nama'), function ($query, $nama) {
                return $query->where('nama', 'like', '%' . $nama . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 10));

        return view('pages.pelanggan.index', compact('pelanggan'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.pelanggan.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $request->validate([
            'nama' => 'required|string|max:255',
            'nomor_hp' => 'nullable|string|max:45',
            'alamat' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'tanggal_bergabung' => 'nullable|date',
        ]);

        PosPelanggan::create([
            'owner_id' => $ownerId,
            'nama' => $request->nama,
            'slug' => \Illuminate\Support\Str::slug($request->nama),
            'nomor_hp' => $request->nomor_hp,
            'alamat' => $request->alamat,
            'email' => $request->email,
            'tanggal_bergabung' => $request->tanggal_bergabung ?? now()->toDateString(),
        ]);

        return redirect()->route('pelanggan.index')->with('success', 'Customer berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $pelanggan = PosPelanggan::where('owner_id', $ownerId)->findOrFail($id);

        return view('pages.pelanggan.show', compact('pelanggan'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(PosPelanggan $pelanggan)
    {
        return view('pages.pelanggan.edit', compact('pelanggan'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  PosPelanggan  $pelanggan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PosPelanggan $pelanggan)
    {
        // Model already injected via route model binding

        $request->validate([
            'nama' => 'required|string|max:255',
            'nomor_hp' => 'nullable|string|max:45',
            'alamat' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'tanggal_bergabung' => 'nullable|date',
        ]);

        $pelanggan->update([
            'nama' => $request->nama,
            'slug' => \Illuminate\Support\Str::slug($request->nama),
            'nomor_hp' => $request->nomor_hp,
            'alamat' => $request->alamat,
            'email' => $request->email,
            'tanggal_bergabung' => $request->tanggal_bergabung,
        ]);

        return redirect()->route('pelanggan.index')->with('success', 'Customer berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(PosPelanggan $pelanggan)
    {
        $pelanggan->delete();

        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil dihapus');
    }

    /**
     * Delete multiple resources.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function bulkDestroy(Request $request)
    {
        $ids = json_decode($request->input('ids'), true);
        
        if (!is_array($ids) || empty($ids)) {
            return redirect()->route('pelanggan.index')->with('error', 'Tidak ada pelanggan yang dipilih');
        }

        PosPelanggan::whereIn('id', $ids)->delete();

        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil dihapus');
    }
}
