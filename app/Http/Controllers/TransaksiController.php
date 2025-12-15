<?php

namespace App\Http\Controllers;

use App\Models\PosTransaksi;
use App\Models\PosTransaksiItem;
use App\Models\PosToko;
use App\Models\PosPelanggan;
use App\Models\PosSupplier;
use App\Traits\UpdatesStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransaksiController extends Controller
{
    use UpdatesStock;
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

        // Process transaction items and update stock if items exist
        if ($request->has('items') && is_array($request->items)) {
            foreach ($request->items as $itemData) {
                if (isset($itemData['pos_produk_id']) || isset($itemData['pos_service_id'])) {
                    // Create transaction item
                    PosTransaksiItem::create([
                        'pos_transaksi_id' => $transaksi->id,
                        'pos_produk_id' => $itemData['pos_produk_id'] ?? null,
                        'pos_service_id' => $itemData['pos_service_id'] ?? null,
                        'quantity' => $itemData['quantity'] ?? 1,
                        'harga_satuan' => $itemData['harga_satuan'] ?? 0,
                        'subtotal' => $itemData['subtotal'] ?? 0,
                        'diskon' => $itemData['diskon'] ?? 0,
                        'garansi' => $itemData['garansi'] ?? null,
                        'garansi_expires_at' => $itemData['garansi_expires_at'] ?? null,
                        'pajak' => $itemData['pajak'] ?? 0,
                    ]);

                    // Update stock for products (not services)
                    if (isset($itemData['pos_produk_id']) && $itemData['pos_produk_id']) {
                        $quantity = $itemData['quantity'] ?? 1;
                        // Transaksi masuk (sales) = stock out (reduce stock)
                        $this->updateProductStock(
                            $ownerId,
                            $request->pos_toko_id,
                            $itemData['pos_produk_id'],
                            -$quantity,
                            'keluar',
                            $request->invoice,
                            'Penjualan produk'
                        );
                    }
                }
            }
        }

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

        // Process transaction items and update stock if items exist
        if ($request->has('items') && is_array($request->items)) {
            foreach ($request->items as $itemData) {
                if (isset($itemData['pos_produk_id']) || isset($itemData['pos_service_id'])) {
                    // Create transaction item
                    PosTransaksiItem::create([
                        'pos_transaksi_id' => $transaksi->id,
                        'pos_produk_id' => $itemData['pos_produk_id'] ?? null,
                        'pos_service_id' => $itemData['pos_service_id'] ?? null,
                        'quantity' => $itemData['quantity'] ?? 1,
                        'harga_satuan' => $itemData['harga_satuan'] ?? 0,
                        'subtotal' => $itemData['subtotal'] ?? 0,
                        'diskon' => $itemData['diskon'] ?? 0,
                        'garansi' => $itemData['garansi'] ?? null,
                        'garansi_expires_at' => $itemData['garansi_expires_at'] ?? null,
                        'pajak' => $itemData['pajak'] ?? 0,
                    ]);

                    // Update stock for products (not services)
                    if (isset($itemData['pos_produk_id']) && $itemData['pos_produk_id']) {
                        $quantity = $itemData['quantity'] ?? 1;
                        // Transaksi keluar (purchase) = stock in (add stock)
                        $this->updateProductStock(
                            $ownerId,
                            $request->pos_toko_id,
                            $itemData['pos_produk_id'],
                            $quantity,
                            'masuk',
                            $request->invoice,
                            'Pembelian produk dari supplier'
                        );
                    }
                }
            }
        }

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

    /**
     * Download transaction report
     */
    public function downloadReport(Request $request)
    {
        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        // Get filters
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $status = $request->get('status');
        
        // Add time to dates for proper filtering
        $startDateTime = $startDate . ' 00:00:00';
        $endDateTime = $endDate . ' 23:59:59';

        // Get transactions
        $transaksi = PosTransaksi::where('owner_id', $ownerId)
            ->with(['toko', 'pelanggan', 'supplier', 'items.produk', 'items.service'])
            ->whereBetween('created_at', [$startDateTime, $endDateTime])
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        // Generate filename
        $filename = 'transactions_report_' . $startDate . '_to_' . $endDate . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($transaksi, $startDate, $endDate) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Report Header
            fputcsv($file, ['TRANSACTIONS REPORT', '', '', '', '', '', '', '', '']);
            fputcsv($file, ['Period', $startDate . ' to ' . $endDate, '', '', '', '', '', '', '']);
            fputcsv($file, ['Generated', now()->format('d/m/Y H:i:s'), '', '', '', '', '', '', '']);
            fputcsv($file, ['', '', '', '', '', '', '', '', '']);
            
            // Column Headers
            fputcsv($file, [
                'Date',
                'Invoice',
                'Type',
                'Store',
                'Customer/Supplier',
                'Payment Method',
                'Status',
                'Total Amount',
                'Items Count'
            ]);
            
            // Data Rows
            foreach ($transaksi as $trans) {
                fputcsv($file, [
                    $trans->created_at->format('d/m/Y H:i'),
                    $trans->invoice,
                    $trans->is_transaksi_masuk ? 'Income' : 'Expense',
                    $trans->toko->nama ?? '-',
                    $trans->is_transaksi_masuk ? ($trans->pelanggan->nama ?? '-') : ($trans->supplier->nama ?? '-'),
                    ucfirst($trans->metode_pembayaran),
                    ucfirst($trans->status),
                    'Rp ' . number_format($trans->total_harga, 0, ',', '.'),
                    $trans->items->count()
                ]);
            }
            
            fputcsv($file, ['', '', '', '', '', '', '', '', '']);
            
            // Summary
            $totalIncome = $transaksi->where('is_transaksi_masuk', 1)->sum('total_harga');
            $totalExpense = $transaksi->where('is_transaksi_masuk', 0)->sum('total_harga');
            
            fputcsv($file, ['SUMMARY', '', '', '', '', '', '', '', '']);
            fputcsv($file, ['Total Transactions', $transaksi->count(), '', '', '', '', '', '', '']);
            fputcsv($file, ['Total Income', 'Rp ' . number_format($totalIncome, 0, ',', '.'), '', '', '', '', '', '', '']);
            fputcsv($file, ['Total Expense', 'Rp ' . number_format($totalExpense, 0, ',', '.'), '', '', '', '', '', '', '']);
            fputcsv($file, ['Net Profit', 'Rp ' . number_format($totalIncome - $totalExpense, 0, ',', '.'), '', '', '', '', '', '', '']);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
