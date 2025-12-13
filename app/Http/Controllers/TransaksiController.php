<?php

namespace App\Http\Controllers;

use App\Models\PosTransaksi;
use App\Models\PosToko;
use App\Models\PosPelanggan;
use App\Models\PosSupplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransaksiController extends Controller
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

        $transaksi = PosTransaksi::where('owner_id', $ownerId)
            ->with(['toko', 'pelanggan', 'supplier'])
            ->when($request->input('invoice'), function ($query, $invoice) {
                return $query->where('invoice', 'like', '%' . $invoice . '%');
            })
            ->when($request->input('status'), function ($query, $status) {
                return $query->where('status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 10));

        return view('pages.transaksi.index', compact('transaksi'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $tokos = PosToko::where('owner_id', $ownerId)->get();
        $pelanggans = PosPelanggan::where('owner_id', $ownerId)->get();
        $suppliers = PosSupplier::where('owner_id', $ownerId)->get();

        // Generate invoice number
        $lastTransaksi = PosTransaksi::where('owner_id', $ownerId)
            ->orderBy('id', 'desc')
            ->first();
        
        $invoiceNumber = 'INV-' . date('Ymd') . '-' . str_pad(($lastTransaksi ? $lastTransaksi->id + 1 : 1), 4, '0', STR_PAD_LEFT);

        return view('pages.transaksi.create', compact('tokos', 'pelanggans', 'suppliers', 'invoiceNumber'));
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
            'pos_toko_id' => 'required',
            'is_transaksi_masuk' => 'required|in:0,1',
            'invoice' => 'required|string|max:45|unique:pos_transaksi,invoice',
            'total_harga' => 'required|numeric|min:0',
            'status' => 'required|string|max:45',
            'metode_pembayaran' => 'required|string|max:45',
            'pos_pelanggan_id' => 'nullable|exists:pos_pelanggan,id',
            'pos_supplier_id' => 'nullable|exists:pos_supplier,id',
            'pos_tukar_tambah_id' => 'nullable|exists:pos_tukar_tambah,id',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $transaksi = PosTransaksi::create([
            'owner_id' => $ownerId,
            'pos_toko_id' => $request->pos_toko_id,
            'pos_pelanggan_id' => $request->pos_pelanggan_id,
            'pos_tukar_tambah_id' => $request->pos_tukar_tambah_id,
            'pos_supplier_id' => $request->pos_supplier_id,
            'is_transaksi_masuk' => $request->is_transaksi_masuk,
            'invoice' => $request->invoice,
            'total_harga' => $request->total_harga,
            'keterangan' => $request->keterangan,
            'status' => $request->status,
            'metode_pembayaran' => $request->metode_pembayaran,
        ]);

        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $transaksi = PosTransaksi::where('owner_id', $ownerId)
            ->with(['toko', 'pelanggan', 'supplier', 'tukarTambah'])
            ->findOrFail($id);

        return view('pages.transaksi.show', compact('transaksi'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $transaksi = PosTransaksi::where('owner_id', $ownerId)->findOrFail($id);
        $tokos = PosToko::where('owner_id', $ownerId)->get();
        $pelanggans = PosPelanggan::where('owner_id', $ownerId)->get();
        $suppliers = PosSupplier::where('owner_id', $ownerId)->get();

        return view('pages.transaksi.edit', compact('transaksi', 'tokos', 'pelanggans', 'suppliers'));
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
        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $transaksi = PosTransaksi::where('owner_id', $ownerId)->findOrFail($id);

        $request->validate([
            'pos_toko_id' => 'required',
            'is_transaksi_masuk' => 'required|in:0,1',
            'invoice' => 'required|string|max:45|unique:pos_transaksi,invoice,' . $id,
            'total_harga' => 'required|numeric|min:0',
            'status' => 'required|string|max:45',
            'metode_pembayaran' => 'required|string|max:45',
            'pos_pelanggan_id' => 'nullable|exists:pos_pelanggan,id',
            'pos_supplier_id' => 'nullable|exists:pos_supplier,id',
            'pos_tukar_tambah_id' => 'nullable|exists:pos_tukar_tambah,id',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $transaksi->update([
            'pos_toko_id' => $request->pos_toko_id,
            'pos_pelanggan_id' => $request->pos_pelanggan_id,
            'pos_tukar_tambah_id' => $request->pos_tukar_tambah_id,
            'pos_supplier_id' => $request->pos_supplier_id,
            'is_transaksi_masuk' => $request->is_transaksi_masuk,
            'invoice' => $request->invoice,
            'total_harga' => $request->total_harga,
            'keterangan' => $request->keterangan,
            'status' => $request->status,
            'metode_pembayaran' => $request->metode_pembayaran,
        ]);

        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $transaksi = PosTransaksi::where('owner_id', $ownerId)->findOrFail($id);
        $transaksi->delete();

        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil dihapus');
    }

    // ============================================
    // INCOMING TRANSACTIONS (SALES) - is_transaksi_masuk = 1
    // ============================================

    public function indexMasuk(Request $request)
    {
        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $transaksi = PosTransaksi::where('owner_id', $ownerId)
            ->where('is_transaksi_masuk', 1)
            ->with(['toko', 'pelanggan'])
            ->when($request->input('invoice'), function ($query, $invoice) {
                return $query->where('invoice', 'like', '%' . $invoice . '%');
            })
            ->when($request->input('status'), function ($query, $status) {
                return $query->where('status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 10));

        return view('pages.transaksi.masuk.index', compact('transaksi'));
    }

    public function createMasuk()
    {
        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $tokos = PosToko::where('owner_id', $ownerId)->get();
        $pelanggans = PosPelanggan::where('owner_id', $ownerId)->get();

        // Generate invoice number
        $lastTransaksi = PosTransaksi::where('owner_id', $ownerId)
            ->orderBy('id', 'desc')
            ->first();
        
        $invoiceNumber = 'INV-' . date('Ymd') . '-' . str_pad(($lastTransaksi ? $lastTransaksi->id + 1 : 1), 4, '0', STR_PAD_LEFT);

        return view('pages.transaksi.masuk.create', compact('tokos', 'pelanggans', 'invoiceNumber'));
    }

    public function storeMasuk(Request $request)
    {
        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $request->validate([
            'pos_toko_id' => 'required',
            'invoice' => 'required|string|max:45|unique:pos_transaksi,invoice',
            'total_harga' => 'required|numeric|min:0',
            'status' => 'required|string|max:45',
            'metode_pembayaran' => 'required|string|max:45',
            'pos_pelanggan_id' => 'nullable|exists:pos_pelanggan,id',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $transaksi = PosTransaksi::create([
            'owner_id' => $ownerId,
            'pos_toko_id' => $request->pos_toko_id,
            'pos_pelanggan_id' => $request->pos_pelanggan_id,
            'is_transaksi_masuk' => 1,
            'invoice' => $request->invoice,
            'total_harga' => $request->total_harga,
            'keterangan' => $request->keterangan,
            'status' => $request->status,
            'metode_pembayaran' => $request->metode_pembayaran,
        ]);

        return redirect()->route('transaksi.masuk.index')->with('success', 'Transaksi masuk berhasil ditambahkan');
    }

    public function editMasuk($id)
    {
        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $transaksi = PosTransaksi::where('owner_id', $ownerId)
            ->where('is_transaksi_masuk', 1)
            ->findOrFail($id);
        
        $tokos = PosToko::where('owner_id', $ownerId)->get();
        $pelanggans = PosPelanggan::where('owner_id', $ownerId)->get();

        return view('pages.transaksi.masuk.edit', compact('transaksi', 'tokos', 'pelanggans'));
    }

    public function updateMasuk(Request $request, $id)
    {
        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $transaksi = PosTransaksi::where('owner_id', $ownerId)
            ->where('is_transaksi_masuk', 1)
            ->findOrFail($id);

        $request->validate([
            'pos_toko_id' => 'required',
            'invoice' => 'required|string|max:45|unique:pos_transaksi,invoice,' . $id,
            'total_harga' => 'required|numeric|min:0',
            'status' => 'required|string|max:45',
            'metode_pembayaran' => 'required|string|max:45',
            'pos_pelanggan_id' => 'nullable|exists:pos_pelanggan,id',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $transaksi->update([
            'pos_toko_id' => $request->pos_toko_id,
            'pos_pelanggan_id' => $request->pos_pelanggan_id,
            'is_transaksi_masuk' => 1,
            'invoice' => $request->invoice,
            'total_harga' => $request->total_harga,
            'keterangan' => $request->keterangan,
            'status' => $request->status,
            'metode_pembayaran' => $request->metode_pembayaran,
        ]);

        return redirect()->route('transaksi.masuk.index')->with('success', 'Transaksi masuk berhasil diperbarui');
    }

    // ============================================
    // OUTGOING TRANSACTIONS (PURCHASES) - is_transaksi_masuk = 0
    // ============================================

    public function indexKeluar(Request $request)
    {
        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $transaksi = PosTransaksi::where('owner_id', $ownerId)
            ->where('is_transaksi_masuk', 0)
            ->with(['toko', 'supplier'])
            ->when($request->input('invoice'), function ($query, $invoice) {
                return $query->where('invoice', 'like', '%' . $invoice . '%');
            })
            ->when($request->input('status'), function ($query, $status) {
                return $query->where('status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 10));

        return view('pages.transaksi.keluar.index', compact('transaksi'));
    }

    public function createKeluar()
    {
        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $tokos = PosToko::where('owner_id', $ownerId)->get();
        $suppliers = PosSupplier::where('owner_id', $ownerId)->get();

        // Generate invoice number
        $lastTransaksi = PosTransaksi::where('owner_id', $ownerId)
            ->orderBy('id', 'desc')
            ->first();
        
        $invoiceNumber = 'INV-' . date('Ymd') . '-' . str_pad(($lastTransaksi ? $lastTransaksi->id + 1 : 1), 4, '0', STR_PAD_LEFT);

        return view('pages.transaksi.keluar.create', compact('tokos', 'suppliers', 'invoiceNumber'));
    }

    public function storeKeluar(Request $request)
    {
        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $request->validate([
            'pos_toko_id' => 'required',
            'invoice' => 'required|string|max:45|unique:pos_transaksi,invoice',
            'total_harga' => 'required|numeric|min:0',
            'status' => 'required|string|max:45',
            'metode_pembayaran' => 'required|string|max:45',
            'pos_supplier_id' => 'nullable|exists:pos_supplier,id',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $transaksi = PosTransaksi::create([
            'owner_id' => $ownerId,
            'pos_toko_id' => $request->pos_toko_id,
            'pos_supplier_id' => $request->pos_supplier_id,
            'is_transaksi_masuk' => 0,
            'invoice' => $request->invoice,
            'total_harga' => $request->total_harga,
            'keterangan' => $request->keterangan,
            'status' => $request->status,
            'metode_pembayaran' => $request->metode_pembayaran,
        ]);

        return redirect()->route('transaksi.keluar.index')->with('success', 'Transaksi keluar berhasil ditambahkan');
    }

    public function editKeluar($id)
    {
        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $transaksi = PosTransaksi::where('owner_id', $ownerId)
            ->where('is_transaksi_masuk', 0)
            ->findOrFail($id);
        
        $tokos = PosToko::where('owner_id', $ownerId)->get();
        $suppliers = PosSupplier::where('owner_id', $ownerId)->get();

        return view('pages.transaksi.keluar.edit', compact('transaksi', 'tokos', 'suppliers'));
    }

    public function updateKeluar(Request $request, $id)
    {
        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $transaksi = PosTransaksi::where('owner_id', $ownerId)
            ->where('is_transaksi_masuk', 0)
            ->findOrFail($id);

        $request->validate([
            'pos_toko_id' => 'required',
            'invoice' => 'required|string|max:45|unique:pos_transaksi,invoice,' . $id,
            'total_harga' => 'required|numeric|min:0',
            'status' => 'required|string|max:45',
            'metode_pembayaran' => 'required|string|max:45',
            'pos_supplier_id' => 'nullable|exists:pos_supplier,id',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $transaksi->update([
            'pos_toko_id' => $request->pos_toko_id,
            'pos_supplier_id' => $request->pos_supplier_id,
            'is_transaksi_masuk' => 0,
            'invoice' => $request->invoice,
            'total_harga' => $request->total_harga,
            'keterangan' => $request->keterangan,
            'status' => $request->status,
            'metode_pembayaran' => $request->metode_pembayaran,
        ]);

        return redirect()->route('transaksi.keluar.index')->with('success', 'Transaksi keluar berhasil diperbarui');
    }
}
