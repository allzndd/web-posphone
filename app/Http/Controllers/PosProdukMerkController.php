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
            ->orderBy('created_at', 'desc')
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
    public function edit($id)
    {
        $merk = PosProdukMerk::withCount('produk')->findOrFail($id);
        return view('pages.pos-produk-merk.edit', compact('merk'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $merk = PosProdukMerk::findOrFail($id);

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
    public function destroy($id)
    {
        $merk = PosProdukMerk::findOrFail($id);
        $merk->delete();

        return redirect()->route('pos-produk-merk.index')->with('success', 'Brand berhasil dihapus');
    }
}
