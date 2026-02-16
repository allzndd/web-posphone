<?php

namespace App\Http\Controllers;

use App\Models\PosService;
use App\Models\PosToko;
use App\Services\PermissionService;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Check read permission
        $hasAccessRead = PermissionService::check('service.read');

        $user = auth()->user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $perPage = $request->get('per_page', 10);
        
        $query = PosService::where('owner_id', $ownerId)
            ->with(['toko'])
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
        $tokos = PosToko::where('owner_id', $ownerId)->orderBy('nama')->get();

        return view('pages.service.index', compact('services', 'tokos', 'hasAccessRead'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Check permission to create
        if (!PermissionService::check('service.create')) {
            return redirect()->route('service.index')->with('error', 'Anda tidak memiliki akses untuk membuat service baru.');
        }

        $user = auth()->user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $tokos = PosToko::where('owner_id', $ownerId)->orderBy('nama')->get();

        return view('pages.service.create', compact('tokos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Check permission to create
        if (!PermissionService::check('service.create')) {
            return redirect()->route('service.index')->with('error', 'Anda tidak memiliki akses untuk membuat service baru.');
        }

        $user = auth()->user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $validated = $request->validate([
            'pos_toko_id' => [
                'required',
                'exists:pos_toko,id',
                function ($attribute, $value, $fail) use ($ownerId) {
                    $toko = PosToko::where('id', $value)->where('owner_id', $ownerId)->first();
                    if (!$toko) {
                        $fail('The selected store is invalid.');
                    }
                },
            ],
            'nama' => 'required|string|max:45',
            'keterangan' => 'nullable|string|max:45',
            'harga' => 'required|numeric|min:0',
            'durasi' => 'nullable|integer|min:0',
        ]);

        $validated['owner_id'] = $ownerId;

        PosService::create($validated);

        return redirect()->route('service.index')
            ->with('success', 'Service created successfully');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PosService $service)
    {
        // Check permission to update
        if (!PermissionService::check('service.update')) {
            return redirect()->route('service.index')->with('error', 'Anda tidak memiliki akses untuk mengedit service.');
        }

        $user = auth()->user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $tokos = PosToko::where('owner_id', $ownerId)->orderBy('nama')->get();

        return view('pages.service.edit', compact('service', 'tokos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PosService $service)
    {
        // Check permission to update
        if (!PermissionService::check('service.update')) {
            return redirect()->route('service.index')->with('error', 'Anda tidak memiliki akses untuk mengubah service.');
        }

        $user = auth()->user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $validated = $request->validate([
            'pos_toko_id' => [
                'required',
                'exists:pos_toko,id',
                function ($attribute, $value, $fail) use ($ownerId) {
                    $toko = PosToko::where('id', $value)->where('owner_id', $ownerId)->first();
                    if (!$toko) {
                        $fail('The selected store is invalid.');
                    }
                },
            ],
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
        // Check permission to delete
        if (!PermissionService::check('service.delete')) {
            return redirect()->route('service.index')->with('error', 'Anda tidak memiliki akses untuk menghapus service.');
        }

        $service->delete();

        return redirect()->route('service.index')
            ->with('success', 'Service deleted successfully');
    }

    /**
     * Bulk delete multiple services.
     */
    public function bulkDestroy(Request $request)
    {
        // Check permission to delete
        if (!PermissionService::check('service.delete')) {
            return redirect()->route('service.index')->with('error', 'Anda tidak memiliki akses untuk menghapus service.');
        }

        $ids = json_decode($request->input('ids'), true);

        if (!is_array($ids) || empty($ids)) {
            return redirect()->route('service.index')
                ->with('error', 'No services selected for deletion');
        }

        $count = PosService::whereIn('id', $ids)->delete();

        return redirect()->route('service.index')
            ->with('success', $count . ' service(s) deleted successfully');
    }
}
