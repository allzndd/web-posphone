<?php

namespace App\Http\Controllers;

use App\Models\PosPenyimpanan;
use App\Models\User;
use Illuminate\Http\Request;

class PosPenyimpananController extends Controller
{
    /**
     * Display a listing of the penyimpanan.
     */
    public function index(Request $request)
    {
        $query = PosPenyimpanan::with('owner');
        
        // If superadmin, only show global items
        if (auth()->user()->role_id === 1) {
            $query->where('id_global', 1);
        }
        // If owner, filter by their ID or global items
        elseif (auth()->user()->role_id === 2) {
            $query->where(function($q) {
                $q->where('id_owner', auth()->id())
                  ->orWhere('id_global', 1);
            });
        } elseif (auth()->user()->role_id === 3) { // role_id 3 = admin
            // Admin can only see global items
            $query->where('id_global', 1);
        }
        
        // Search by kapasitas
        if ($request->has('kapasitas') && !empty($request->get('kapasitas'))) {
            $query->where('kapasitas', 'like', '%' . $request->get('kapasitas') . '%');
        }
        
        $perPage = $request->get('per_page', 15);
        $posPenyimpanans = $query->orderBy('created_at', 'desc')->paginate($perPage);
        return view('pages.pos-penyimpanan.index', compact('posPenyimpanans'));
    }

    /**
     * Show the form for creating a new penyimpanan.
     */
    public function create()
    {
        // Only super admin can select owner; for other roles, id_owner is auto-set
        $owners = auth()->user()->role_id === 1 ? User::where('role_id', 2)->get() : collect();
        return view('pages.pos-penyimpanan.create', compact('owners'));
    }

    /**
     * Store a newly created penyimpanan in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_owner' => 'nullable|integer',
            'kapasitas' => 'required|integer|min:0',
            'id_global' => 'nullable|integer',
        ]);

        // For superadmin, set id_owner to null and id_global to 1
        if (auth()->user()->role_id === 1) {
            $validated['id_owner'] = null;
            $validated['id_global'] = 1;
        }
        // For owner, set id_owner to their ID and id_global to 0
        elseif (auth()->user()->role_id === 2) {
            $validated['id_owner'] = auth()->id();
            $validated['id_global'] = 0;
        }
        // For admin, id_global should be 1
        elseif (auth()->user()->role_id === 3) {
            $validated['id_global'] = 1;
        }

        PosPenyimpanan::create($validated);

        return redirect()->route('pos-penyimpanan.index')
            ->with('success', 'Penyimpanan berhasil ditambahkan');
    }

    /**
     * Display the specified penyimpanan.
     */
    public function show(PosPenyimpanan $posPenyimpanan)
    {
        return view('pages.pos-penyimpanan.show', compact('posPenyimpanan'));
    }

    /**
     * Show the form for editing the specified penyimpanan.
     */
    public function edit(PosPenyimpanan $posPenyimpanan)
    {
        // Check authorization for owner - only can edit their own items
        if (auth()->user()->role_id === 2 && $posPenyimpanan->id_owner !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Only super admin can select owner; for other roles, id_owner is auto-set
        $owners = auth()->user()->role_id === 1 ? User::where('role_id', 2)->get() : collect();
        return view('pages.pos-penyimpanan.edit', compact('posPenyimpanan', 'owners'));
    }

    /**
     * Update the specified penyimpanan in storage.
     */
    public function update(Request $request, PosPenyimpanan $posPenyimpanan)
    {
        // Check authorization for owner - only can update their own items, not global items
        if (auth()->user()->role_id === 2 && $posPenyimpanan->id_owner !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'id_owner' => 'nullable|integer',
            'kapasitas' => 'required|integer|min:0',
            'id_global' => 'nullable|integer',
        ]);

        // For superadmin, set id_owner to null and id_global to 1
        if (auth()->user()->role_id === 1) {
            $validated['id_owner'] = null;
            $validated['id_global'] = 1;
        }
        // For owner, set id_owner to their ID and id_global to 0
        elseif (auth()->user()->role_id === 2) {
            $validated['id_owner'] = auth()->id();
            $validated['id_global'] = 0;
        }
        // For admin, id_global should be 1
        elseif (auth()->user()->role_id === 3) {
            $validated['id_global'] = 1;
        }

        $posPenyimpanan->update($validated);

        return redirect()->route('pos-penyimpanan.index')
            ->with('success', 'Penyimpanan berhasil diperbarui');
    }

    /**
     * Remove the specified penyimpanan from storage.
     */
    public function destroy(PosPenyimpanan $posPenyimpanan)
    {
        // Check authorization for owner - only can delete their own items, not global items
        if (auth()->user()->role_id === 2 && $posPenyimpanan->id_owner !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $posPenyimpanan->delete();

        return redirect()->route('pos-penyimpanan.index')
            ->with('success', 'Penyimpanan berhasil dihapus');
    }

    /**
     * Delete multiple penyimpanan items
     */
    public function bulkDestroy(Request $request)
    {
        $ids = json_decode($request->input('ids'), true);
        
        if (!is_array($ids) || empty($ids)) {
            return redirect()->route('pos-penyimpanan.index')
                ->with('error', 'Tidak ada data yang dipilih');
        }

        $query = PosPenyimpanan::whereIn('id', $ids);
        
        // If owner, only allow deleting their own items
        if (auth()->user()->role_id === 2) {
            $query->where('id_owner', auth()->id());
        }
        
        $query->delete();

        return redirect()->route('pos-penyimpanan.index')
            ->with('success', 'Penyimpanan berhasil dihapus');
    }
}
