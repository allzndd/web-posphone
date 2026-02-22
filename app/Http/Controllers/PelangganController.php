<?php

namespace App\Http\Controllers;

use App\Models\PosPelanggan;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PelangganController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Check permission read
        $hasAccessRead = PermissionService::check('customer.read');

        $ownerId = $user->owner ? $user->owner->id : null;

        $pelanggan = PosPelanggan::where('owner_id', $ownerId)
            ->when($request->input('nama'), function ($query, $nama) {
                return $query->where('nama', 'like', '%' . $nama . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 10));

        // Pass permission flags to view
        $canCreate = PermissionService::check('customer.create');
        $canUpdate = PermissionService::check('customer.update');
        $canDelete = PermissionService::check('customer.delete');
        $hasActions = $canUpdate || $canDelete;

        return view('pages.pelanggan.index', compact('pelanggan', 'canCreate', 'canUpdate', 'canDelete', 'hasActions', 'hasAccessRead'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Check permission create
        if (!PermissionService::check('customer.create')) {
            return redirect('/')->with('error', 'Anda tidak memiliki akses untuk membuat customer baru');
        }

        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        // Get current customer count and max records limit
        $currentCount = PosPelanggan::where('owner_id', $ownerId)->count();
        $maxRecords = PermissionService::getMaxRecords('customer.create');

        return view('pages.pelanggan.create', compact('currentCount', 'maxRecords'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Check permission create
        if (!PermissionService::check('customer.create')) {
            $message = 'Anda tidak memiliki akses untuk membuat customer baru';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 403);
            }
            return redirect('/')->with('error', $message);
        }

        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        // Check max records limit
        $currentCount = PosPelanggan::where('owner_id', $ownerId)->count();
        $maxRecords = PermissionService::getMaxRecords('customer.create');

        if (PermissionService::isReachedLimit('customer.create', $currentCount)) {
            $message = 'Anda telah mencapai batas maksimal data customer (' . $maxRecords . ' records)';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            return redirect()->back()
                ->withInput()
                ->with('error', $message);
        }

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nomor_hp' => 'nullable|string|max:45',
            'alamat' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'tanggal_bergabung' => 'nullable|date',
        ]);

        $pelanggan = PosPelanggan::create([
            'owner_id' => $ownerId,
            'nama' => $validated['nama'],
            'slug' => \Illuminate\Support\Str::slug($validated['nama']),
            'nomor_hp' => $validated['nomor_hp'] ?? null,
            'alamat' => $validated['alamat'] ?? null,
            'email' => $validated['email'] ?? null,
            'tanggal_bergabung' => $validated['tanggal_bergabung'] ?? now()->toDateString(),
        ]);

        // Return JSON for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Customer berhasil ditambahkan',
                'id' => $pelanggan->id,
                'nama' => $pelanggan->nama
            ], 201);
        }

        return redirect()->route('pelanggan.index')->with('success', 'Customer berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $pelanggan = PosPelanggan::where('owner_id', $ownerId)->findOrFail($id);

        return view('pages.pelanggan.show', compact('pelanggan'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(PosPelanggan $pelanggan)
    {
        // Check permission update
        if (!PermissionService::check('customer.update')) {
            return redirect('/')->with('error', 'Anda tidak memiliki akses untuk mengubah customer');
        }

        // Pass permission flags to view
        $canUpdate = PermissionService::check('customer.update');
        $canDelete = PermissionService::check('customer.delete');

        return view('pages.pelanggan.edit', compact('pelanggan', 'canUpdate', 'canDelete'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  PosPelanggan  $pelanggan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PosPelanggan $pelanggan)
    {
        // Check permission update
        if (!PermissionService::check('customer.update')) {
            return redirect('/')->with('error', 'Anda tidak memiliki akses untuk mengubah customer');
        }

        // Model already injected via route model binding

        $request->validate([
            'nama' => 'required|string|max:255',
            'nomor_hp' => 'nullable|string|max:45',
            'alamat' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'tanggal_bergabung' => 'nullable|date',
        ]);

        $pelanggan->update([
            'nama' => $request->nama,
            'slug' => \Illuminate\Support\Str::slug($request->nama),
            'nomor_hp' => $request->nomor_hp,
            'alamat' => $request->alamat,
            'email' => $request->email,
            'tanggal_bergabung' => $request->tanggal_bergabung,
        ]);

        return redirect()->route('pelanggan.index')->with('success', 'Customer berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(PosPelanggan $pelanggan)
    {
        // Check permission delete
        if (!PermissionService::check('customer.delete')) {
            return redirect('/')->with('error', 'Anda tidak memiliki akses untuk menghapus customer');
        }

        $pelanggan->delete();

        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil dihapus');
    }

    /**
     * Delete multiple resources.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function bulkDestroy(Request $request)
    {
        // Check permission delete
        if (!PermissionService::check('customer.delete')) {
            return redirect('/')->with('error', 'Anda tidak memiliki akses untuk menghapus customer');
        }

        $ids = json_decode($request->input('ids'), true);
        
        if (!is_array($ids) || empty($ids)) {
            return redirect()->route('pelanggan.index')->with('error', 'Tidak ada pelanggan yang dipilih');
        }

        PosPelanggan::whereIn('id', $ids)->delete();

        return redirect()->route('pelanggan.index')->with('success', 'Pelanggan berhasil dihapus');
    }
}
