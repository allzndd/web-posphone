<?php

namespace App\Http\Controllers;

use App\Models\TipeLayanan;
use App\Models\Permission;
use App\Models\PackagePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaketLayananController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $paket = TipeLayanan::with('packagePermissions.permission')->latest()->get();
        return view('paket-layanan.index', compact('paket'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = Permission::orderBy('modul', 'asc')
            ->orderBy('aksi', 'asc')
            ->get()
            ->groupBy('modul');
        
        return view('paket-layanan.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'harga' => 'nullable|numeric|min:0',
            'durasi' => 'required|integer|min:1',
            'durasi_satuan' => 'nullable|in:hari,bulan,tahun',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
            'max_records' => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            // Auto-generate slug
            $validated['slug'] = \Illuminate\Support\Str::slug($validated['nama']);
            
            // Set default durasi_satuan if not provided
            if (!isset($validated['durasi_satuan'])) {
                $validated['durasi_satuan'] = 'bulan';
            }

            $paket = TipeLayanan::create($validated);

            // Simpan package permissions
            if (!empty($request->permissions)) {
                foreach ($request->permissions as $permissionId) {
                    $maxRecords = $request->max_records[$permissionId] ?? 0;
                    
                    PackagePermission::create([
                        'tipe_layanan_id' => $paket->id,
                        'permissions_id' => $permissionId,
                        'max_records' => $maxRecords ? (int)$maxRecords : 0,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('paket-layanan.index')->with('success', 'Service package successfully created');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Failed to create package: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $paket = TipeLayanan::findOrFail($id);
        return view('paket-layanan.show', compact('paket'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $paket = TipeLayanan::with('packagePermissions')->findOrFail($id);
        
        $permissions = Permission::orderBy('modul', 'asc')
            ->orderBy('aksi', 'asc')
            ->get()
            ->groupBy('modul');
        
        // Get selected permissions with max_records
        $selectedPermissions = $paket->packagePermissions->pluck('permissions_id')->toArray();
        $maxRecordsData = $paket->packagePermissions->pluck('max_records', 'permissions_id')->toArray();
        
        return view('paket-layanan.edit', compact('paket', 'permissions', 'selectedPermissions', 'maxRecordsData'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'harga' => 'nullable|numeric|min:0',
            'durasi' => 'required|integer|min:1',
            'durasi_satuan' => 'nullable|in:hari,bulan,tahun',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
            'max_records' => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            // Auto-generate slug
            $validated['slug'] = \Illuminate\Support\Str::slug($validated['nama']);
            
            // Set default durasi_satuan if not provided
            if (!isset($validated['durasi_satuan'])) {
                $validated['durasi_satuan'] = 'bulan';
            }

            $paket = TipeLayanan::findOrFail($id);
            $paket->update($validated);

            // Delete existing permissions
            PackagePermission::where('tipe_layanan_id', $paket->id)->delete();

            // Simpan package permissions baru
            if (!empty($request->permissions)) {
                foreach ($request->permissions as $permissionId) {
                    $maxRecords = $request->max_records[$permissionId] ?? 0;
                    
                    PackagePermission::create([
                        'tipe_layanan_id' => $paket->id,
                        'permissions_id' => $permissionId,
                        'max_records' => $maxRecords ? (int)$maxRecords : 0,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('paket-layanan.index')->with('success', 'Service package successfully updated');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Failed to update package: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $paket = TipeLayanan::findOrFail($id);
        $paket->delete();

        return redirect()->route('paket-layanan.index')->with('success', 'Service package successfully deleted');
    }
}
