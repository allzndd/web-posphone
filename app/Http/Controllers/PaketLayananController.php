<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaketLayananController extends Controller
{
    private $dummy = [
        ['id' => 1, 'nama' => 'Paket Starter', 'deskripsi' => 'Sistem POS Web + Mobile (1 Outlet)', 'harga' => 500000, 'durasi' => '1 Bulan', 'status' => 'aktif'],
        ['id' => 2, 'nama' => 'Paket Basic', 'deskripsi' => 'Sistem POS Web + Mobile (3 Outlet) + Support', 'harga' => 1200000, 'durasi' => '3 Bulan', 'status' => 'aktif'],
        ['id' => 3, 'nama' => 'Paket Professional', 'deskripsi' => 'Sistem POS Web + Mobile (5 Outlet) + Priority Support', 'harga' => 2500000, 'durasi' => '6 Bulan', 'status' => 'aktif'],
        ['id' => 4, 'nama' => 'Paket Premium', 'deskripsi' => 'Sistem POS Web + Mobile (10 Outlet) + Custom Feature', 'harga' => 5000000, 'durasi' => '1 Tahun', 'status' => 'aktif'],
        ['id' => 5, 'nama' => 'Paket Enterprise', 'deskripsi' => 'Sistem POS Web + Mobile (Unlimited Outlet) + Dedicated Support', 'harga' => 10000000, 'durasi' => '1 Tahun', 'status' => 'nonaktif'],
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
