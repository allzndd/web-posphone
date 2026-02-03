<?php

namespace App\Http\Controllers;

use App\Models\PosRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PosRoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $roles = PosRole::where('owner_id', $ownerId)
            ->when($request->input('nama'), function ($query, $nama) {
                return $query->where('nama', 'like', '%' . $nama . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 10));

        return view('pages.pos-role.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.pos-role.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        // Validate request
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        $role = PosRole::create([
            'owner_id' => $ownerId,
            'nama' => $validated['nama'],
        ]);

        // Return JSON for AJAX requests
        if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'role' => $role,
                'message' => 'Role berhasil ditambahkan'
            ], 201);
        }

        return redirect()->route('pos-role.index')->with('success', 'Role berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PosRole $posRole)
    {
        $role = $posRole;
        return view('pages.pos-role.edit', compact('role'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PosRole $posRole)
    {
        $role = $posRole;

        $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        $role->update([
            'nama' => $request->nama,
        ]);

        return redirect()->route('pos-role.index')->with('success', 'Role berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PosRole $posRole)
    {
        $posRole->delete();

        return redirect()->route('pos-role.index')->with('success', 'Role berhasil dihapus');
    }

    /**
     * Bulk delete roles
     */
    public function bulkDestroy(Request $request)
    {
        $ids = json_decode($request->ids, true);
        
        if (!is_array($ids) || empty($ids)) {
            return redirect()->back()->with('error', 'Pilih minimal satu role untuk dihapus');
        }

        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $deletedCount = PosRole::where('owner_id', $ownerId)
            ->whereIn('id', $ids)
            ->delete();
        
        return redirect()->route('pos-role.index')
            ->with('success', $deletedCount . ' role berhasil dihapus');
    }
}
