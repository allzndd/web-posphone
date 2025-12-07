<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use Illuminate\Http\Request;

class LayananController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $layanan = Layanan::latest()->get();
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
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'harga' => 'required|numeric|min:0',
            'status' => 'required|in:Active,Inactive',
        ]);

        Layanan::create($validated);

        return redirect()->route('layanan.index')->with('success', 'Service successfully created');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $item = Layanan::findOrFail($id);
        return view('layanan.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $item = Layanan::findOrFail($id);
        return view('layanan.edit', compact('item'));
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
            'status' => 'required|in:Active,Inactive',
        ]);

        $item = Layanan::findOrFail($id);
        $item->update($validated);

        return redirect()->route('layanan.index')->with('success', 'Service successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $item = Layanan::findOrFail($id);
        $item->delete();

        return redirect()->route('layanan.index')->with('success', 'Service successfully deleted');
    }
}
