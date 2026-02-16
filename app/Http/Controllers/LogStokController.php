<?php

namespace App\Http\Controllers;

use App\Models\LogStok;
use App\Services\PermissionService;
use Illuminate\Http\Request;

class LogStokController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Check read permission
        $hasAccessRead = PermissionService::check('log-stok.read');

        $user = auth()->user();
        $ownerId = $user->owner ? $user->owner->id : null;
        $perPage = $request->get('per_page', 10);
        
        $query = LogStok::with(['produk', 'toko', 'pengguna'])
            ->where('owner_id', $ownerId)
            ->orderBy('created_at', 'desc');

        // Filter by type
        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        // Filter by product
        if ($request->filled('pos_produk_id')) {
            $query->where('pos_produk_id', $request->pos_produk_id);
        }

        // Filter by store
        if ($request->filled('pos_toko_id')) {
            $query->where('pos_toko_id', $request->pos_toko_id);
        }

        // Search by reference
        if ($request->filled('referensi')) {
            $query->where('referensi', 'like', '%' . $request->referensi . '%');
        }

        $logs = $query->paginate($perPage);

        return view('pages.log-stok.index', compact('logs', 'hasAccessRead'));
    }
}
