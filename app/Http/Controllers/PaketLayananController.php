<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaketLayananController extends Controller
{
    private $dummy = [
        ['id' => 1, 'nama' => 'Paket Basic', 'deskripsi' => 'Service + Cleaning', 'harga' => 150000, 'durasi' => '1 hari', 'status' => 'aktif'],
        ['id' => 2, 'nama' => 'Paket Premium', 'deskripsi' => 'Service + Ganti Spare Part', 'harga' => 500000, 'durasi' => '2 hari', 'status' => 'aktif'],
        ['id' => 3, 'nama' => 'Paket Ultimate', 'deskripsi' => 'Service Lengkap + Garansi 6 Bulan', 'harga' => 1000000, 'durasi' => '3 hari', 'status' => 'nonaktif'],
    ];

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $paket = $this->dummy;
        return view('paket-layanan.index', compact('paket'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('paket-layanan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return redirect()->route('paket-layanan.index')->with('success', 'Data paket layanan berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $paket = collect($this->dummy)->firstWhere('id', (int)$id);
        return view('paket-layanan.show', compact('paket'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $paket = collect($this->dummy)->firstWhere('id', (int)$id);
        return view('paket-layanan.edit', compact('paket'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        return redirect()->route('paket-layanan.index')->with('success', 'Data paket layanan berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return redirect()->route('paket-layanan.index')->with('success', 'Data paket layanan berhasil dihapus');
    }
}
