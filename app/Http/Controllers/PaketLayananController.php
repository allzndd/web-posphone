<?php

namespace App\Http\Controllers;

use App\Models\PaketLayanan;
use Illuminate\Http\Request;

class PaketLayananController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $paket = PaketLayanan::latest()->get();
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
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'harga' => 'required|numeric|min:0',
            'durasi' => 'required|string|max:100',
            'status' => 'required|in:Active,Inactive',
        ]);

        PaketLayanan::create($validated);

        return redirect()->route('paket-layanan.index')->with('success', 'Service package successfully created');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $paket = PaketLayanan::findOrFail($id);
        return view('paket-layanan.show', compact('paket'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $paket = PaketLayanan::findOrFail($id);
        return view('paket-layanan.edit', compact('paket'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'harga' => 'required|numeric|min:0',
            'durasi' => 'required|string|max:100',
            'status' => 'required|in:Active,Inactive'
        ]);

        $paket = PaketLayanan::findOrFail($id);
        $paket->update($validated);

        return redirect()->route('paket-layanan.index')->with('success', 'Service package successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $paket = PaketLayanan::findOrFail($id);
        $paket->delete();

        return redirect()->route('paket-layanan.index')->with('success', 'Service package successfully deleted');
    }
}
