<?php

namespace App\Http\Controllers;

use App\Models\PosProdukMerk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PosProdukMerkController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $isSuperadmin = $user->hasRole('SUPERADMIN');
        
        if ($isSuperadmin) {
            // Superadmin sees only global items (is_global = 1, owner_id = null)
            $merks = PosProdukMerk::where('is_global', 1)
                ->whereNull('owner_id')
                ->withCount('produk')
                ->when($request->input('nama'), function ($query, $nama) {
                    return $query->where('nama', 'like', '%' . $nama . '%');
                })
                ->when($request->input('merk'), function ($query, $merk) {
                    return $query->where('merk', 'like', '%' . $merk . '%');
                })
                ->orderBy('id', 'desc')
                ->paginate($request->input('per_page', 10));
        } else {
            // Owner/Admin sees only their own items
            $ownerId = $user->owner ? $user->owner->id : null;
            $merks = PosProdukMerk::where('owner_id', $ownerId)
                ->withCount('produk')
                ->when($request->input('nama'), function ($query, $nama) {
                    return $query->where('nama', 'like', '%' . $nama . '%');
                })
                ->when($request->input('merk'), function ($query, $merk) {
                    return $query->where('merk', 'like', '%' . $merk . '%');
                })
                ->orderBy('id', 'desc')
                ->paginate($request->input('per_page', 10));
        }

        return view('pages.pos-produk-merk.index', compact('merks'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.pos-produk-merk.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $isSuperadmin = $user->hasRole('SUPERADMIN');

        $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        if ($isSuperadmin) {
            // Superadmin: is_global = 1, owner_id = null
            PosProdukMerk::create([
                'owner_id' => null,
                'nama' => $request->nama,
                'is_global' => 1,
            ]);
        } else {
            // Owner/Admin: owner_id = their owner_id, is_global = 0 or null
            $ownerId = $user->owner ? $user->owner->id : null;
            PosProdukMerk::create([
                'owner_id' => $ownerId,
                'nama' => $request->nama,
                'is_global' => 0,
            ]);
        }

        return redirect()->route('pos-produk-merk.index')->with('success', 'Brand berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(PosProdukMerk $posProdukMerk)
    {
        $merk = $posProdukMerk->loadCount('produk');
        return view('pages.pos-produk-merk.edit', compact('merk'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  PosProdukMerk  $posProdukMerk
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PosProdukMerk $posProdukMerk)
    {
        $merk = $posProdukMerk;
        $user = Auth::user();
        $isSuperadmin = $user->hasRole('SUPERADMIN');

        $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        $updateData = [
            'nama' => $request->nama,
        ];

        // Ensure superadmin items stay global
        if ($isSuperadmin) {
            $updateData['is_global'] = 1;
            $updateData['owner_id'] = null;
        }

        $merk->update($updateData);

        return redirect()->route('pos-produk-merk.index')->with('success', 'Brand berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(PosProdukMerk $posProdukMerk)
    {
        $merk = $posProdukMerk;
        $merk->delete();

        return redirect()->route('pos-produk-merk.index')->with('success', 'Brand berhasil dihapus');
    }

    /**
     * Bulk delete product brands
     */
    public function bulkDestroy(Request $request)
    {
        $ids = json_decode($request->ids, true);
        
        if (!is_array($ids) || empty($ids)) {
            return redirect()->back()->with('error', 'Pilih minimal satu brand untuk dihapus');
        }

        $user = Auth::user();
        $isSuperadmin = $user->hasRole('SUPERADMIN');

        if ($isSuperadmin) {
            // Superadmin can only delete global items
            $deletedCount = PosProdukMerk::where('is_global', 1)
                ->whereNull('owner_id')
                ->whereIn('id', $ids)
                ->delete();
        } else {
            // Owner/Admin can only delete their own items
            $ownerId = $user->owner ? $user->owner->id : null;
            $deletedCount = PosProdukMerk::where('owner_id', $ownerId)
                ->whereIn('id', $ids)
                ->delete();
        }
        
        return redirect()->route('pos-produk-merk.index')
            ->with('success', $deletedCount . ' brand berhasil dihapus');
    }

    /**
     * Quick store product name from AJAX modal (for product forms)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function quickStore(Request $request)
    {
        try {
            $user = Auth::user();
            $isSuperadmin = $user->hasRole('SUPERADMIN');

            $validatedData = $request->validate([
                'nama' => 'required|string|max:255',
            ]);

            if ($isSuperadmin) {
                $merk = PosProdukMerk::create([
                    'owner_id' => null,
                    'nama' => $validatedData['nama'],
                    'is_global' => 1,
                ]);
            } else {
                $ownerId = $user->owner ? $user->owner->id : null;
                $merk = PosProdukMerk::create([
                    'owner_id' => $ownerId,
                    'nama' => $validatedData['nama'],
                    'is_global' => 0,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Product Name created successfully!',
                'data' => $merk
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create product name: ' . $e->getMessage()
            ], 500);
        }
    }
}
