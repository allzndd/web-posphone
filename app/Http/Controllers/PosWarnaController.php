<?php

namespace App\Http\Controllers;

use App\Models\PosWarna;
use App\Models\User;
use App\Services\PermissionService;
use Illuminate\Http\Request;

class PosWarnaController extends Controller
{
    /**
     * Display a listing of the warna.
     */
    public function index(Request $request)
    {
        $hasAccessRead = PermissionService::check('pos-warna.read');
        
        $query = PosWarna::with('owner');
        
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
        
        // Search by warna
        if ($request->has('warna') && !empty($request->get('warna'))) {
            $query->where('warna', 'like', '%' . $request->get('warna') . '%');
        }
        
        $perPage = $request->get('per_page', 15);
        $posWarnas = $query->orderBy('created_at', 'desc')->paginate($perPage);
        return view('pages.pos-warna.index', compact('posWarnas', 'hasAccessRead'));
    }

    /**
     * Show the form for creating a new warna.
     */
    public function create()
    {
        if (!PermissionService::check('pos-warna.create')) {
            return redirect('/')->with('error', 'You do not have permission to create colors');
        }
        // Only super admin can select owner; for other roles, id_owner is auto-set
        $owners = auth()->user()->role_id === 1 ? User::where('role_id', 2)->get() : collect();
        return view('pages.pos-warna.create', compact('owners'));
    }

    /**
     * Store a newly created warna in storage.
     */
    public function store(Request $request)
    {
        if (!PermissionService::check('pos-warna.create')) {
            return redirect('/')->with('error', 'You do not have permission to create colors');
        }
        $validated = $request->validate([
            'id_owner' => 'nullable|integer',
            'warna' => 'required|string|max:100',
            'is_global' => 'nullable|integer',
        ]);

        // For superadmin, set id_owner to null and is_global to 1
        if (auth()->user()->role_id === 1) {
            $validated['id_owner'] = null;
            $validated['is_global'] = 1;
        }
        // For owner, set id_owner to their ID and is_global to 0
        elseif (auth()->user()->role_id === 2) {
            $validated['id_owner'] = auth()->id();
            $validated['is_global'] = 0;
        }
        // For admin, is_global should be 1
        elseif (auth()->user()->role_id === 3) {
            $validated['is_global'] = 1;
        }

        PosWarna::create($validated);

        return redirect()->route('pos-warna.index')
            ->with('success', 'Warna berhasil ditambahkan');
    }

    /**
     * Display the specified warna.
     */
    public function show(PosWarna $posWarna)
    {
        return view('pages.pos-warna.show', compact('posWarna'));
    }

    /**
     * Show the form for editing the specified warna.
     */
    public function edit(PosWarna $posWarna)
    {
        if (!PermissionService::check('pos-warna.update')) {
            return redirect('/')->with('error', 'You do not have permission to edit colors');
        }
        // Check authorization for owner - only can edit their own items
        if (auth()->user()->role_id === 2 && $posWarna->id_owner !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Only super admin can select owner; for other roles, id_owner is auto-set
        $owners = auth()->user()->role_id === 1 ? User::where('role_id', 2)->get() : collect();
        return view('pages.pos-warna.edit', compact('posWarna', 'owners'));
    }

    /**
     * Update the specified warna in storage.
     */
    public function update(Request $request, PosWarna $posWarna)
    {
        if (!PermissionService::check('pos-warna.update')) {
            return redirect('/')->with('error', 'You do not have permission to edit colors');
        }
        // Check authorization for owner - only can update their own items, not global items
        if (auth()->user()->role_id === 2 && $posWarna->id_owner !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'id_owner' => 'nullable|integer',
            'warna' => 'required|string|max:100',
            'is_global' => 'nullable|integer',
        ]);

        // For superadmin, set id_owner to null and is_global to 1
        if (auth()->user()->role_id === 1) {
            $validated['id_owner'] = null;
            $validated['is_global'] = 1;
        }
        // For owner, set id_owner to their ID and is_global to 0
        elseif (auth()->user()->role_id === 2) {
            $validated['id_owner'] = auth()->id();
            $validated['is_global'] = 0;
        }
        // For admin, is_global should be 1
        elseif (auth()->user()->role_id === 3) {
            $validated['is_global'] = 1;
        }

        $posWarna->update($validated);

        return redirect()->route('pos-warna.index')
            ->with('success', 'Warna berhasil diperbarui');
    }

    /**
     * Remove the specified warna from storage.
     */
    public function destroy(PosWarna $posWarna)
    {
        if (!PermissionService::check('pos-warna.delete')) {
            return redirect('/')->with('error', 'You do not have permission to delete colors');
        }
        // Check authorization for owner - only can delete their own items, not global items
        if (auth()->user()->role_id === 2 && $posWarna->id_owner !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $posWarna->delete();

        return redirect()->route('pos-warna.index')
            ->with('success', 'Warna berhasil dihapus');
    }

    /**
     * Delete multiple warna items
     */
    public function bulkDestroy(Request $request)
    {
        if (!PermissionService::check('pos-warna.delete')) {
            return redirect('/')->with('error', 'You do not have permission to delete colors');
        }
        $ids = json_decode($request->input('ids'), true);
        
        if (!is_array($ids) || empty($ids)) {
            return redirect()->route('pos-warna.index')
                ->with('error', 'Tidak ada data yang dipilih');
        }

        $query = PosWarna::whereIn('id', $ids);
        
        // If owner, only allow deleting their own items
        if (auth()->user()->role_id === 2) {
            $query->where('id_owner', auth()->id());
        }
        
        $query->delete();

        return redirect()->route('pos-warna.index')
            ->with('success', 'Warna berhasil dihapus');
    }
}
