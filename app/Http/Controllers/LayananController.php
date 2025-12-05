<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LayananController extends Controller
{
    private $dummy = [
        ['id' => 1, 'nama' => 'Service HP', 'deskripsi' => 'Perbaikan kerusakan hardware dan software', 'harga' => 100000, 'status' => 'aktif'],
        ['id' => 2, 'nama' => 'Ganti LCD', 'deskripsi' => 'Penggantian LCD rusak atau pecah', 'harga' => 500000, 'status' => 'aktif'],
        ['id' => 3, 'nama' => 'Upgrade RAM', 'deskripsi' => 'Peningkatan kapasitas RAM', 'harga' => 300000, 'status' => 'nonaktif'],
    ];

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $layanan = $this->dummy;
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
        return redirect()->route('layanan.index')->with('success', 'Data layanan berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $item = collect($this->dummy)->firstWhere('id', (int)$id);
        return view('layanan.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $item = collect($this->dummy)->firstWhere('id', (int)$id);
        return view('layanan.edit', compact('item'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        return redirect()->route('layanan.index')->with('success', 'Data layanan berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return redirect()->route('layanan.index')->with('success', 'Data layanan berhasil dihapus');
    }
}
