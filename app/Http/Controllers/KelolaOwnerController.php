<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KelolaOwnerController extends Controller
{
    private $dummy = [
        [
            'id' => 1,
            'nama_perusahaan' => 'PT Maju Jaya',
            'nama_pemilik' => 'Budi Santoso',
            'email' => 'majujaya@gmail.com',
            'telepon' => '08123456789',
            'paket' => 'Paket Premium',
            'tanggal_daftar' => '2025-01-15',
            'tanggal_expired' => '2026-01-15',
            'jumlah_outlet' => 10,
            'status' => 'Aktif'
        ],
        [
            'id' => 2,
            'nama_perusahaan' => 'CV Berkah Store',
            'nama_pemilik' => 'Siti Aminah',
            'email' => 'berkahstore@gmail.com',
            'telepon' => '08198765432',
            'paket' => 'Paket Basic',
            'tanggal_daftar' => '2025-03-20',
            'tanggal_expired' => '2025-12-20',
            'jumlah_outlet' => 3,
            'status' => 'Aktif'
        ],
        [
            'id' => 3,
            'nama_perusahaan' => 'Toko Elektronik Jaya',
            'nama_pemilik' => 'Ahmad Dahlan',
            'email' => 'elektronikjaya@gmail.com',
            'telepon' => '08567891234',
            'paket' => 'Paket Enterprise',
            'tanggal_daftar' => '2024-06-10',
            'tanggal_expired' => '2025-12-10',
            'jumlah_outlet' => 25,
            'status' => 'Aktif'
        ],
        [
            'id' => 4,
            'nama_perusahaan' => 'Warung Kopi Nusantara',
            'nama_pemilik' => 'Dewi Lestari',
            'email' => 'kopinusantara@gmail.com',
            'telepon' => '08234567890',
            'paket' => 'Paket Starter',
            'tanggal_daftar' => '2025-10-01',
            'tanggal_expired' => '2025-11-01',
            'jumlah_outlet' => 1,
            'status' => 'Expired'
        ],
        [
            'id' => 5,
            'nama_perusahaan' => 'Minimarket Sejahtera',
            'nama_pemilik' => 'Eko Prasetyo',
            'email' => 'minimarketsejahtera@gmail.com',
            'telepon' => '08345678901',
            'paket' => 'Paket Professional',
            'tanggal_daftar' => '2025-05-12',
            'tanggal_expired' => '2025-11-12',
            'jumlah_outlet' => 5,
            'status' => 'Expired'
        ],
    ];

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $owners = $this->dummy;
        return view('kelola-owner.index', compact('owners'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('kelola-owner.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return redirect()->route('kelola-owner.index')->with('success', 'Data owner berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $owner = collect($this->dummy)->firstWhere('id', (int)$id);
        return view('kelola-owner.show', compact('owner'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $owner = collect($this->dummy)->firstWhere('id', (int)$id);
        return view('kelola-owner.edit', compact('owner'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        return redirect()->route('kelola-owner.index')->with('success', 'Data owner berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return redirect()->route('kelola-owner.index')->with('success', 'Data owner berhasil dihapus');
    }
}
