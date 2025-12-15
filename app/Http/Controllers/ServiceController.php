<?php

namespace App\Http\Controllers;

use App\Models\PosService;
use App\Models\PosToko;
use App\Models\PosPelanggan;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        
        $query = PosService::with(['toko', 'pelanggan'])
            ->orderBy('created_at', 'desc');

        // Search by name
        if ($request->filled('nama')) {
            $query->where('nama', 'like', '%' . $request->nama . '%');
        }

        // Filter by store
        if ($request->filled('pos_toko_id')) {
            $query->where('pos_toko_id', $request->pos_toko_id);
        }

        $services = $query->paginate($perPage);
        $tokos = PosToko::orderBy('nama')->get();

        return view('pages.service.index', compact('services', 'tokos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tokos = PosToko::orderBy('nama')->get();
        $pelanggans = PosPelanggan::orderBy('nama')->get();

        return view('pages.service.create', compact('tokos', 'pelanggans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pos_toko_id' => 'required|exists:pos_toko,id',
            'pos_pelanggan_id' => 'nullable|exists:pos_pelanggan,id',
            'nama' => 'required|string|max:45',
            'keterangan' => 'nullable|string|max:45',
            'harga' => 'required|numeric|min:0',
            'durasi' => 'nullable|integer|min:0',
        ]);

        $user = auth()->user();
        $validated['owner_id'] = $user->owner ? $user->owner->id : null;

        PosService::create($validated);

        return redirect()->route('service.index')
            ->with('success', 'Service created successfully');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PosService $service)
    {
        $tokos = PosToko::orderBy('nama')->get();
        $pelanggans = PosPelanggan::orderBy('nama')->get();

        return view('pages.service.edit', compact('service', 'tokos', 'pelanggans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PosService $service)
    {
        $validated = $request->validate([
            'pos_toko_id' => 'required|exists:pos_toko,id',
            'pos_pelanggan_id' => 'nullable|exists:pos_pelanggan,id',
            'nama' => 'required|string|max:45',
            'keterangan' => 'nullable|string|max:45',
            'harga' => 'required|numeric|min:0',
            'durasi' => 'nullable|integer|min:0',
        ]);

        $service->update($validated);

        return redirect()->route('service.index')
            ->with('success', 'Service updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PosService $service)
    {
        $service->delete();

        return redirect()->route('service.index')
            ->with('success', 'Service deleted successfully');
    }
}
