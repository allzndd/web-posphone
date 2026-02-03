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
        $ownerId = $user->owner ? $user->owner->id : null;

        $merks = PosProdukMerk::where('owner_id', $ownerId)
            ->withCount('produk')
            ->when($request->input('nama'), function ($query, $nama) {
                return $query->where('nama', 'like', '%' . $nama . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate($request->input('per_page', 10));

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
        $ownerId = $user->owner ? $user->owner->id : null;

        $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        PosProdukMerk::create([
            'owner_id' => $ownerId,
            'nama' => $request->nama,
        ]);

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

        $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        $merk->update([
            'nama' => $request->nama,
        ]);

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
        $ownerId = $user->owner ? $user->owner->id : null;

        $deletedCount = PosProdukMerk::where('owner_id', $ownerId)
            ->whereIn('id', $ids)
            ->delete();
        
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
            $ownerId = $user->owner ? $user->owner->id : null;

            $validatedData = $request->validate([
                'nama' => 'required|string|max:255',
            ]);

            $merk = PosProdukMerk::create([
                'owner_id' => $ownerId,
                'nama' => $validatedData['nama'],
            ]);

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
