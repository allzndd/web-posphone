<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $permissions = Permission::orderBy('modul', 'asc')
            ->orderBy('aksi', 'asc')
            ->get();
        
        return view('permissions.index', compact('permissions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $pages = $this->getAvailablePages();
        return view('permissions.create', compact('pages'));
    }

    /**
     * Get available pages from resources/views/pages folder
     */
    private function getAvailablePages()
    {
        $pagesPath = resource_path('views/pages');
        $pages = [];

        if (is_dir($pagesPath)) {
            $items = array_diff(scandir($pagesPath), ['.', '..']);
            
            foreach ($items as $item) {
                $itemPath = $pagesPath . '/' . $item;
                // Skip auth folder dan files, hanya ambil folders
                if (is_dir($itemPath) && $item !== 'auth') {
                    $pages[] = ucfirst(str_replace('-', ' ', $item));
                }
            }
            
            sort($pages);
        }

        return $pages;
    }

    /**
     * Store a newly created resource in storage.
     * Membuat permissions sesuai aksi yang dipilih
     */
    public function store(Request $request)
    {
        $request->validate([
            'modul' => 'required|string|max:100',
            'aksi' => 'required|array|min:1',
            'aksi.*' => 'in:create,read,update,delete',
        ], [
            'modul.required' => 'Nama modul wajib diisi',
            'aksi.required' => 'Minimal pilih 1 aksi',
            'aksi.min' => 'Minimal pilih 1 aksi',
        ]);

        DB::beginTransaction();
        try {
            $modul = $request->modul;
            $actions = $request->aksi;
            $created = 0;
            $skipped = [];

            foreach ($actions as $action) {
                // Cek apakah kombinasi modul+aksi sudah ada
                $exists = Permission::where('modul', $modul)
                    ->where('aksi', $action)
                    ->exists();

                if (!$exists) {
                    Permission::create([
                        'nama' => $modul . '.' . $action,
                        'modul' => $modul,
                        'aksi' => $action,
                    ]);
                    $created++;
                } else {
                    $skipped[] = $action;
                }
            }

            DB::commit();
            
            $message = "Berhasil menambahkan {$created} permissions untuk modul {$modul}";
            if (!empty($skipped)) {
                $message .= " (" . implode(', ', $skipped) . " sudah ada)";
            }
            
            return redirect()->route('permissions.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Gagal menambahkan permissions: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $permission = Permission::findOrFail($id);
        return view('permissions.edit', compact('permission'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:150|unique:permissions,nama,' . $id,
            'modul' => 'required|string|max:100',
            'aksi' => 'required|string|max:100',
        ], [
            'nama.required' => 'Nama permission wajib diisi',
            'nama.unique' => 'Nama permission sudah ada',
            'modul.required' => 'Modul wajib diisi',
            'aksi.required' => 'Aksi wajib diisi',
        ]);

        try {
            $permission->update([
                'nama' => $request->nama,
                'modul' => $request->modul,
                'aksi' => $request->aksi,
            ]);

            return redirect()->route('permissions.index')
                ->with('success', 'Permission berhasil diupdate');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal mengupdate permission: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $permission = Permission::findOrFail($id);
            $permission->delete();

            return redirect()->route('permissions.index')
                ->with('success', 'Permission berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus permission: ' . $e->getMessage());
        }
    }

    /**
     * Delete all permissions for a specific module
     */
    public function destroyModule(Request $request)
    {
        $request->validate([
            'modul' => 'required|string',
        ]);

        try {
            Permission::where('modul', $request->modul)->delete();

            return redirect()->route('permissions.index')
                ->with('success', 'Semua permissions untuk modul ' . $request->modul . ' berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus permissions: ' . $e->getMessage());
        }
    }

    /**
     * Bulk delete multiple permissions
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|json',
        ]);

        try {
            $ids = json_decode($request->ids, true);
            
            if (!is_array($ids) || empty($ids)) {
                return redirect()->route('permissions.index')
                    ->with('error', 'Tidak ada permission yang dipilih');
            }

            // Delete permissions
            Permission::whereIn('id', $ids)->delete();

            return redirect()->route('permissions.index')
                ->with('success', 'Berhasil menghapus ' . count($ids) . ' permission(s)');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus permissions: ' . $e->getMessage());
        }
    }
}
