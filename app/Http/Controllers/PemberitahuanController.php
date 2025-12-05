<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PemberitahuanController extends Controller
{
    private $dummy = [
        ['id' => 1, 'judul' => 'Promo Akhir Tahun', 'pesan' => 'Diskon 50% untuk semua layanan', 'tujuan' => 'Semua Customer', 'tanggal' => '2025-12-01', 'status' => 'terkirim'],
        ['id' => 2, 'judul' => 'Maintenance System', 'pesan' => 'System akan maintenance pada tanggal 10 Desember', 'tujuan' => 'Staff', 'tanggal' => '2025-12-05', 'status' => 'draft'],
        ['id' => 3, 'judul' => 'Update Layanan', 'pesan' => 'Layanan baru telah ditambahkan', 'tujuan' => 'Customer Premium', 'tanggal' => '2025-11-28', 'status' => 'terkirim'],
    ];

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pemberitahuan = $this->dummy;
        return view('pemberitahuan.index', compact('pemberitahuan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pemberitahuan.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return redirect()->route('pemberitahuan.index')->with('success', 'Pemberitahuan berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $item = collect($this->dummy)->firstWhere('id', (int)$id);
        return view('pemberitahuan.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $item = collect($this->dummy)->firstWhere('id', (int)$id);
        return view('pemberitahuan.edit', compact('item'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        return redirect()->route('pemberitahuan.index')->with('success', 'Pemberitahuan berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return redirect()->route('pemberitahuan.index')->with('success', 'Pemberitahuan berhasil dihapus');
    }
}
