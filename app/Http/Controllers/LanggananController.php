<?php

namespace App\Http\Controllers;

use App\Models\Langganan;
use App\Models\Owner;
use App\Models\TipeLayanan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LanggananController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $langganan = Langganan::with(['owner.pengguna', 'tipeLayanan'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('langganan.index', compact('langganan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $owners = Owner::with('pengguna')->get();
        $paketLayanan = TipeLayanan::all();
        
        return view('langganan.create', compact('owners', 'paketLayanan'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'owner_id' => 'required|exists:owner,id',
            'tipe_layanan_id' => 'required|exists:tipe_layanan,id',
            'started_date' => 'required|date',
            'is_trial' => 'boolean',
            'is_active' => 'boolean',
        ]);

        // Get paket layanan untuk hitung end_date
        $paket = TipeLayanan::findOrFail($validated['tipe_layanan_id']);
        
        // Hitung end_date berdasarkan durasi paket (dalam bulan)
        $startDate = Carbon::parse($validated['started_date']);
        $endDate = $startDate->copy()->addMonths($paket->durasi);
        
        $validated['end_date'] = $endDate;
        $validated['is_trial'] = $request->has('is_trial') ? 1 : 0;
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        Langganan::create($validated);

        return redirect()->route('langganan.index')
            ->with('success', 'Subscription successfully created');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $item = Langganan::with(['owner.pengguna', 'tipeLayanan', 'pembayaran'])
            ->findOrFail($id);
        
        return view('langganan.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $item = Langganan::with(['owner.pengguna', 'tipeLayanan'])->findOrFail($id);
        $owners = Owner::with('pengguna')->get();
        $paketLayanan = TipeLayanan::all();
        
        return view('langganan.edit', compact('item', 'owners', 'paketLayanan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $langganan = Langganan::findOrFail($id);

        $validated = $request->validate([
            'owner_id' => 'required|exists:owner,id',
            'tipe_layanan_id' => 'required|exists:tipe_layanan,id',
            'started_date' => 'required|date',
            'end_date' => 'required|date|after:started_date',
            'is_trial' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['is_trial'] = $request->has('is_trial') ? 1 : 0;
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $langganan->update($validated);

        return redirect()->route('langganan.index')
            ->with('success', 'Subscription successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $langganan = Langganan::findOrFail($id);
        $langganan->delete();

        return redirect()->route('langganan.index')
            ->with('success', 'Subscription successfully deleted');
    }

    /**
     * Toggle active status
     */
    public function toggleActive(string $id)
    {
        $langganan = Langganan::findOrFail($id);
        $langganan->is_active = !$langganan->is_active;
        $langganan->save();

        $status = $langganan->is_active ? 'activated' : 'deactivated';
        
        return redirect()->route('langganan.index')
            ->with('success', "Subscription successfully {$status}");
    }
}
