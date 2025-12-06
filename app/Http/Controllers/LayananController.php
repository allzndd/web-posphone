<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LayananController extends Controller
{
    private $dummy = [
        ['id' => 1, 'nama' => 'Setup Akun Owner', 'deskripsi' => 'Pembuatan akun owner baru untuk sistem POS', 'harga' => 0, 'status' => 'aktif'],
        ['id' => 2, 'nama' => 'Migrasi Data', 'deskripsi' => 'Layanan migrasi data dari sistem lama ke sistem baru', 'harga' => 2000000, 'status' => 'aktif'],
        ['id' => 3, 'nama' => 'Training & Support', 'deskripsi' => 'Pelatihan penggunaan sistem untuk tim owner', 'harga' => 1500000, 'status' => 'aktif'],
        ['id' => 4, 'nama' => 'Custom Development', 'deskripsi' => 'Pengembangan fitur khusus sesuai kebutuhan owner', 'harga' => 5000000, 'status' => 'aktif'],
        ['id' => 5, 'nama' => 'Maintenance Premium', 'deskripsi' => 'Layanan maintenance dan support prioritas 24/7', 'harga' => 3000000, 'status' => 'nonaktif'],
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
