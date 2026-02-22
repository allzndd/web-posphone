<?php

namespace App\Http\Controllers;

use App\Models\PosRam;
use App\Models\User;
use App\Services\PermissionService;
use Illuminate\Http\Request;

class PosRamController extends Controller
{
    /**
     * Display a listing of the ram.
     */
    public function index(Request $request)
    {
        $hasAccessRead = PermissionService::check('pos-ram.read');
        
        $query = PosRam::with('owner');
        
        // If superadmin, only show global items
        if (auth()->user()->role_id === 1) {
            $query->where('is_global', 1);
        }
        // If owner, filter by their ID or global items
        elseif (auth()->user()->role_id === 2) {
            $query->where(function($q) {
                $q->where('id_owner', auth()->id())
                  ->orWhere('is_global', 1);
            });
        } elseif (auth()->user()->role_id === 3) { // role_id 3 = admin
            // Admin can only see global items
            $query->where('is_global', 1);
        }
        
        // Search implementation
        if ($request->has('kapasitas') && !empty($request->get('kapasitas'))) {
            $query->where('kapasitas', 'like', '%' . $request->get('kapasitas') . '%');
        }
        
        $perPage = $request->get('per_page', 15);
        $posRams = $query->orderBy('created_at', 'desc')->paginate($perPage);
        return view('pages.pos-ram.index', compact('posRams', 'hasAccessRead'));
    }

    /**
     * Show the form for creating a new ram.
     */
    public function create()
    {
        if (!PermissionService::check('pos-ram.create')) {
            return redirect('/')->with('error', 'You do not have permission to create RAM');
        }
        // Only super admin can select owner; for other roles, id_owner is auto-set
        $owners = auth()->user()->role_id === 1 ? User::where('role_id', 2)->get() : collect();
        return view('pages.pos-ram.create', compact('owners'));
    }

    /**
     * Store a newly created ram in storage.
     */
    public function store(Request $request)
    {
        if (!PermissionService::check('pos-ram.create')) {
            return redirect('/')->with('error', 'You do not have permission to create RAM');
        }
        $validated = $request->validate([
            'id_owner' => 'nullable|integer',
            'kapasitas' => 'required|integer|min:0',
            'is_global' => 'nullable|integer',
        ]);

        // For superadmin, set id_owner to null and is_global to 1
        if (auth()->user()->role_id === 1) {
            $validated['id_owner'] = null;
            $validated['is_global'] = 1;
        }
        // For owner, set id_owner to their ID and is_global to 1 (can use in transactions)
        elseif (auth()->user()->role_id === 2) {
            $validated['id_owner'] = auth()->id();
            $validated['is_global'] = 1;
        }
        // For admin, is_global should be 1
        elseif (auth()->user()->role_id === 3) {
            $validated['is_global'] = 1;
        }

        PosRam::create($validated);

        return redirect()->route('pos-ram.index')
            ->with('success', 'RAM berhasil ditambahkan');
    }

    /**
     * Display the specified ram.
     */
    public function show(PosRam $posRam)
    {
        // Check authorization for owner - only can view their own items
        if (auth()->user()->role_id === 2 && $posRam->id_owner !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        
        return view('pages.pos-ram.show', compact('posRam'));
    }

    /**
     * Show the form for editing the specified ram.
     */
    public function edit(PosRam $posRam)
    {
        if (!PermissionService::check('pos-ram.update')) {
            return redirect('/')->with('error', 'You do not have permission to edit RAM');
        }
        // Check authorization for owner - only can edit their own items
        if (auth()->user()->role_id === 2 && $posRam->id_owner !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Only super admin can select owner; for other roles, id_owner is auto-set
        $owners = auth()->user()->role_id === 1 ? User::where('role_id', 2)->get() : collect();
        return view('pages.pos-ram.edit', compact('posRam', 'owners'));
    }

    /**
     * Update the specified ram in storage.
     */
    public function update(Request $request, PosRam $posRam)
    {
        if (!PermissionService::check('pos-ram.update')) {
            return redirect('/')->with('error', 'You do not have permission to edit RAM');
        }
        // Check authorization for owner - only can update their own items, not global items
        if (auth()->user()->role_id === 2 && $posRam->id_owner !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'id_owner' => 'nullable|integer',
            'kapasitas' => 'required|integer|min:0',
            'is_global' => 'nullable|integer',
        ]);

        // For superadmin, set id_owner to null and is_global to 1
        if (auth()->user()->role_id === 1) {
            $validated['id_owner'] = null;
            $validated['is_global'] = 1;
        }
        // For owner, set id_owner to their ID and is_global to 1 (can use in transactions)
        elseif (auth()->user()->role_id === 2) {
            $validated['id_owner'] = auth()->id();
            $validated['is_global'] = 1;
        }
        // For admin, is_global should be 1
        elseif (auth()->user()->role_id === 3) {
            $validated['is_global'] = 1;
        }

        $posRam->update($validated);

        return redirect()->route('pos-ram.index')
            ->with('success', 'RAM berhasil diperbarui');
    }

    /**
     * Remove the specified ram from storage.
     */
    public function destroy(PosRam $posRam)
    {
        if (!PermissionService::check('pos-ram.delete')) {
            return redirect('/')->with('error', 'You do not have permission to delete RAM');
        }
        // Check authorization for owner - only can delete their own items, not global items
        if (auth()->user()->role_id === 2 && $posRam->id_owner !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $posRam->delete();

        return redirect()->route('pos-ram.index')
            ->with('success', 'RAM berhasil dihapus');
    }

    /**
     * Delete multiple ram items
     */
    public function bulkDestroy(Request $request)
    {
        if (!PermissionService::check('pos-ram.delete')) {
            return redirect('/')->with('error', 'You do not have permission to delete RAM');
        }
        $ids = json_decode($request->input('ids'), true);
        
        if (!is_array($ids) || empty($ids)) {
            return redirect()->route('pos-ram.index')
                ->with('error', 'Tidak ada data yang dipilih');
        }

        $query = PosRam::whereIn('id', $ids);
        
        // If owner, only allow deleting their own items
        if (auth()->user()->role_id === 2) {
            $query->where('id_owner', auth()->id());
        }
        
        $query->delete();

        return redirect()->route('pos-ram.index')
            ->with('success', 'RAM berhasil dihapus');
    }
}
