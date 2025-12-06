<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    private $dummy = [
        ['id' => 1, 'tanggal' => '2025-12-01', 'owner' => 'PT Maju Jaya', 'email' => 'majujaya@gmail.com', 'paket' => 'Paket Premium', 'periode' => '1 Bulan', 'total' => 500000, 'status' => 'Lunas'],
        ['id' => 2, 'tanggal' => '2025-12-05', 'owner' => 'CV Berkah Store', 'email' => 'berkah@yahoo.com', 'paket' => 'Paket Basic', 'periode' => '3 Bulan', 'total' => 1200000, 'status' => 'Pending'],
        ['id' => 3, 'tanggal' => '2025-11-28', 'owner' => 'Toko Elektronik Jaya', 'email' => 'elektronik@gmail.com', 'paket' => 'Paket Enterprise', 'periode' => '1 Tahun', 'total' => 5000000, 'status' => 'Lunas'],
    ];

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pembayaran = $this->dummy;
        return view('pembayaran.index', compact('pembayaran'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pembayaran.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return redirect()->route('pembayaran.index')->with('success', 'Pembayaran berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $item = collect($this->dummy)->firstWhere('id', (int)$id);
        return view('pembayaran.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $item = collect($this->dummy)->firstWhere('id', (int)$id);
        return view('pembayaran.edit', compact('item'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        return redirect()->route('pembayaran.index')->with('success', 'Pembayaran berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return redirect()->route('pembayaran.index')->with('success', 'Pembayaran berhasil dihapus');
    }
}
