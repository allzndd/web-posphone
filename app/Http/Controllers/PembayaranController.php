<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Langganan;
use App\Models\Owner;
use App\Models\TipeLayanan;

class PembayaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pembayaran = Langganan::with(['owner.pengguna', 'tipeLayanan'])
            ->whereHas('owner') // Only show langganan with existing owner
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pembayaran.index', compact('pembayaran'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $owners = Owner::with('pengguna')->get();
        $tipeLayanan = TipeLayanan::all();

        return view('pembayaran.create', compact('owners', 'tipeLayanan'));
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

        $validated['is_trial'] = $request->has('is_trial') ? 1 : 0;
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $tipeLayanan = TipeLayanan::findOrFail($validated['tipe_layanan_id']);
        $startDate = \Carbon\Carbon::parse($validated['started_date']);
        
        // Handle different duration units (with backward compatibility)
        $durasiSatuan = $tipeLayanan->durasi_satuan ?? 'bulan'; // default to bulan if column doesn't exist
        
        if ($durasiSatuan === 'hari') {
            $endDate = $startDate->copy()->addDays($tipeLayanan->durasi);
        } elseif ($durasiSatuan === 'tahun') {
            $endDate = $startDate->copy()->addYears($tipeLayanan->durasi);
        } else {
            // Default to months
            $endDate = $startDate->copy()->addMonths($tipeLayanan->durasi);
        }
        
        $validated['end_date'] = $endDate;

        Langganan::create($validated);

        return redirect()->route('pembayaran.index')
            ->with('success', 'Subscription successfully added');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $item = Langganan::with(['owner.pengguna', 'tipeLayanan'])->findOrFail($id);

        return view('pembayaran.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $item = Langganan::with(['owner.pengguna', 'tipeLayanan'])->findOrFail($id);
        $owners = Owner::with('pengguna')->get();
        $tipeLayanan = TipeLayanan::all();

        return view('pembayaran.edit', compact('item', 'owners', 'tipeLayanan'));
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
            'is_trial' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['is_trial'] = $request->has('is_trial') ? 1 : 0;
        $validated['is_active'] = $request->has('is_active') ? 1 : 0;

        $tipeLayanan = TipeLayanan::findOrFail($validated['tipe_layanan_id']);
        $startDate = \Carbon\Carbon::parse($validated['started_date']);
        
        // Handle different duration units (with backward compatibility)
        $durasiSatuan = $tipeLayanan->durasi_satuan ?? 'bulan'; // default to bulan if column doesn't exist
        
        if ($durasiSatuan === 'hari') {
            $endDate = $startDate->copy()->addDays($tipeLayanan->durasi);
        } elseif ($durasiSatuan === 'tahun') {
            $endDate = $startDate->copy()->addYears($tipeLayanan->durasi);
        } else {
            // Default to months
            $endDate = $startDate->copy()->addMonths($tipeLayanan->durasi);
        }
        
        $validated['end_date'] = $endDate;

        $langganan->update($validated);

        return redirect()->route('pembayaran.index')
            ->with('success', 'Subscription successfully updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $langganan = Langganan::findOrFail($id);
        $langganan->delete();

        return redirect()->route('pembayaran.index')
            ->with('success', 'Subscription successfully deleted');
    }
}
