<?php

namespace App\Http\Controllers;

use App\Models\PosPengguna;
use App\Models\PosRole;
use App\Models\PosToko;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PosPenggunaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $pengguna = PosPengguna::where('owner_id', $ownerId)
            ->with(['role', 'toko'])
            ->when($request->input('nama'), function ($query, $nama) {
                return $query->where('nama', 'like', '%' . $nama . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 10));

        return view('pages.pos-pengguna.index', compact('pengguna'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $roles = PosRole::where('owner_id', $ownerId)->get();
        $tokos = PosToko::where('owner_id', $ownerId)->get();

        return view('pages.pos-pengguna.create', compact('roles', 'tokos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:pos_pengguna,email',
            'password' => 'required|string|min:6',
            'pos_role_id' => 'required|exists:pos_role,id',
            'pos_toko_id' => 'nullable|exists:pos_toko,id',
        ]);

        PosPengguna::create([
            'owner_id' => $ownerId,
            'pos_toko_id' => $request->pos_toko_id,
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'pos_role_id' => $request->pos_role_id,
        ]);

        return redirect()->route('pos-pengguna.index')->with('success', 'Pengguna berhasil ditambahkan');
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
    public function edit(PosPengguna $posPengguna)
    {
        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $pengguna = $posPengguna;
        $roles = PosRole::where('owner_id', $ownerId)->get();
        $tokos = PosToko::where('owner_id', $ownerId)->get();

        return view('pages.pos-pengguna.edit', compact('pengguna', 'roles', 'tokos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PosPengguna $posPengguna)
    {
        $pengguna = $posPengguna;

        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:pos_pengguna,email,' . $pengguna->id,
            'password' => 'nullable|string|min:6',
            'pos_role_id' => 'required|exists:pos_role,id',
            'pos_toko_id' => 'nullable|exists:pos_toko,id',
        ]);

        $data = [
            'pos_toko_id' => $request->pos_toko_id,
            'nama' => $request->nama,
            'email' => $request->email,
            'pos_role_id' => $request->pos_role_id,
        ];

        // Only update password if provided
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $pengguna->update($data);

        return redirect()->route('pos-pengguna.index')->with('success', 'Pengguna berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PosPengguna $posPengguna)
    {
        $pengguna = $posPengguna;
        $pengguna->delete();

        return redirect()->route('pos-pengguna.index')->with('success', 'Pengguna berhasil dihapus');
    }
}
