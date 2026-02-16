<?php

namespace App\Http\Controllers;

use App\Models\PosRole;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PosRoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Check read permission
        $hasAccessRead = PermissionService::check('pos-role.read');

        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $roles = PosRole::where('owner_id', $ownerId)
            ->when($request->input('nama'), function ($query, $nama) {
                return $query->where('nama', 'like', '%' . $nama . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 10));

        return view('pages.pos-role.index', compact('roles', 'hasAccessRead'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Check permission to create
        if (!PermissionService::check('pos-role.create')) {
            return redirect()->route('pos-role.index')->with('error', 'Anda tidak memiliki akses untuk membuat role baru.');
        }

        return view('pages.pos-role.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Check permission to create
        if (!PermissionService::check('pos-role.create')) {
            return redirect()->route('pos-role.index')->with('error', 'Anda tidak memiliki akses untuk membuat role baru.');
        }

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
        // Check permission to update
        if (!PermissionService::check('pos-role.update')) {
            return redirect()->route('pos-role.index')->with('error', 'Anda tidak memiliki akses untuk mengedit role.');
        }

        $role = $posRole;
        return view('pages.pos-role.edit', compact('role'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PosRole $posRole)
    {
        // Check permission to update
        if (!PermissionService::check('pos-role.update')) {
            return redirect()->route('pos-role.index')->with('error', 'Anda tidak memiliki akses untuk mengubah role.');
        }

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
        // Check permission to delete
        if (!PermissionService::check('pos-role.delete')) {
            return redirect()->route('pos-role.index')->with('error', 'Anda tidak memiliki akses untuk menghapus role.');
        }

        $posRole->delete();

        return redirect()->route('pos-role.index')->with('success', 'Role berhasil dihapus');
    }

    /**
     * Bulk delete roles
     */
    public function bulkDestroy(Request $request)
    {
        // Check permission to delete
        if (!PermissionService::check('pos-role.delete')) {
            return redirect()->route('pos-role.index')->with('error', 'Anda tidak memiliki akses untuk menghapus role.');
        }

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
