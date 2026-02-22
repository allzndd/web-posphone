<?php

namespace App\Http\Controllers;

use App\Models\PosToko;
use App\Models\Langganan;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TokoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Check read permission
        $hasAccessRead = PermissionService::check('toko.read');

        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        // Check Free Tier status - match by slug OR nama 'Free Tier'
        $subscription = Langganan::where('owner_id', $ownerId)
            ->with('tipeLayanan')
            ->latest()
            ->first();
        
        $isFreeTier = false;
        if ($subscription && $subscription->tipeLayanan) {
            $isFreeTier = $subscription->tipeLayanan->slug === 'free' || 
                         strtolower($subscription->tipeLayanan->nama) === 'free tier';
        }
        
        $tokoCount = PosToko::where('owner_id', $ownerId)->count();
        $canAddToko = !$isFreeTier || ($isFreeTier && $tokoCount < 1);

        $tokos = PosToko::where('owner_id', $ownerId)
            ->when($request->input('nama'), function ($query, $nama) {
                return $query->where('nama', 'like', '%' . $nama . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 10));

        return view('pages.toko.index', compact('tokos', 'hasAccessRead', 'isFreeTier', 'tokoCount', 'canAddToko'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Check permission to create
        if (!PermissionService::check('toko.create')) {
            return redirect()->route('toko.index')->with('error', 'Anda tidak memiliki akses untuk membuat toko baru.');
        }

        // Check Free Tier limitation - match by slug OR nama 'Free Tier'
        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;
        
        $subscription = Langganan::where('owner_id', $ownerId)
            ->with('tipeLayanan')
            ->latest()
            ->first();
        
        $isFreeTier = false;
        if ($subscription && $subscription->tipeLayanan) {
            $isFreeTier = $subscription->tipeLayanan->slug === 'free' || 
                         strtolower($subscription->tipeLayanan->nama) === 'free tier';
        }
        
        $tokoCount = 0;
        $canAddToko = true;
        
        if ($isFreeTier) {
            $tokoCount = PosToko::where('owner_id', $ownerId)->count();
            if ($tokoCount >= 1) {
                $warningMessage = 'Paket Free Tier hanya membolehkan 1 toko. Silakan upgrade paket untuk menambah toko lebih banyak.';
                return redirect()->route('toko.index')->with('warning', $warningMessage);
            }
        }

        return view('pages.toko.create', compact('isFreeTier', 'tokoCount', 'canAddToko'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Check permission to create
        if (!PermissionService::check('toko.create')) {
            return redirect()->route('toko.index')->with('error', 'Anda tidak memiliki akses untuk membuat toko baru.');
        }

        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        // Validate Free Tier limitation - match by slug OR nama 'Free Tier'
        $subscription = Langganan::where('owner_id', $ownerId)
            ->with('tipeLayanan')
            ->latest()
            ->first();
        
        $isFreeTier = false;
        if ($subscription && $subscription->tipeLayanan) {
            $isFreeTier = $subscription->tipeLayanan->slug === 'free' || 
                         strtolower($subscription->tipeLayanan->nama) === 'free tier';
        }
        
        if ($isFreeTier) {
            $tokoCount = PosToko::where('owner_id', $ownerId)->count();
            if ($tokoCount >= 1) {
                return redirect()->route('toko.index')
                    ->with('error', 'Paket Free Tier hanya membolehkan 1 toko. Silakan upgrade paket Anda untuk menambah toko lebih banyak.');
            }
        }

        $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'modal' => 'nullable|numeric|min:0|max:9999999999999.99',
        ]);

        $toko = PosToko::create([
            'owner_id' => $ownerId,
            'nama' => $request->nama,
            'alamat' => $request->alamat,
            'modal' => $request->modal,
        ]);

        // Check if request is AJAX
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Toko berhasil ditambahkan',
                'toko' => $toko
            ], 200);
        }

        return redirect()->route('toko.index')->with('success', 'Toko berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $toko = PosToko::where('owner_id', $ownerId)
            ->where('id', $id)
            ->with(['pengguna.role'])
            ->firstOrFail();

        return view('pages.toko.show', compact('toko'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PosToko $toko)
    {
        // Check permission to update
        if (!PermissionService::check('toko.update')) {
            return redirect()->route('toko.index')->with('error', 'Anda tidak memiliki akses untuk mengedit toko.');
        }

        return view('pages.toko.edit', compact('toko'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PosToko $toko)
    {
        // Check permission to update
        if (!PermissionService::check('toko.update')) {
            return redirect()->route('toko.index')->with('error', 'Anda tidak memiliki akses untuk mengubah toko.');
        }

        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        // Verify ownership
        if ($toko->owner_id !== $ownerId) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'modal' => 'nullable|numeric|min:0|max:9999999999999.99',
        ]);

        $toko->update([
            'nama' => $request->nama,
            'alamat' => $request->alamat,
            'modal' => $request->modal,
        ]);

        return redirect()->route('toko.index')->with('success', 'Toko berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PosToko $toko)
    {
        // Check permission to delete
        if (!PermissionService::check('toko.delete')) {
            return redirect()->route('toko.index')->with('error', 'Anda tidak memiliki akses untuk menghapus toko.');
        }

        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        // Verify ownership
        if ($toko->owner_id !== $ownerId) {
            abort(403, 'Unauthorized action.');
        }

        $toko->delete();

        return redirect()->route('toko.index')->with('success', 'Toko berhasil dihapus');
    }

    /**
     * Bulk delete stores
     */
    public function bulkDestroy(Request $request)
    {
        // Check permission to delete
        if (!PermissionService::check('toko.delete')) {
            return redirect()->route('toko.index')->with('error', 'Anda tidak memiliki akses untuk menghapus toko.');
        }

        $ids = json_decode($request->ids, true);
        
        if (!is_array($ids) || empty($ids)) {
            return redirect()->back()->with('error', 'Pilih minimal satu toko untuk dihapus');
        }

        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $deletedCount = PosToko::where('owner_id', $ownerId)
            ->whereIn('id', $ids)
            ->delete();
        
        return redirect()->route('toko.index')
            ->with('success', $deletedCount . ' toko berhasil dihapus');
    }
}
