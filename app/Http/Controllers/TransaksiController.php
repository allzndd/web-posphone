<?php

namespace App\Http\Controllers;

use App\Models\PosTransaksi;
use App\Models\PosTransaksiItem;
use App\Models\PosToko;
use App\Models\PosPelanggan;
use App\Models\PosSupplier;
use App\Models\PosProduk;
use App\Models\PosService;
use App\Traits\UpdatesStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    use UpdatesStock;
    
    /**
     * Generate unique invoice number with retry mechanism
     * 
     * @param int $ownerId
     * @param bool $isMasuk (true for incoming/sales, false for outgoing/purchases)
     * @return string
     */
    private function generateInvoiceNumber($ownerId, $isMasuk = true)
    {
        $maxRetries = 10;
        $attempt = 0;
        
        while ($attempt < $maxRetries) {
            // Use different prefix for incoming vs outgoing
            $prefix = $isMasuk ? 'INV-IN-' : 'INV-OUT-';
            
            // Get last transaction of this type only
            $lastTransaksi = PosTransaksi::where('owner_id', $ownerId)
                ->where('is_transaksi_masuk', $isMasuk ? 1 : 0)
                ->where('invoice', 'like', $prefix . '%')
                ->orderBy('id', 'desc')
                ->first();
            
            $dateStr = date('Ymd');
            $nextNumber = 1;
            
            if ($lastTransaksi && $lastTransaksi->invoice) {
                // Parse last invoice: INV-IN-YYYYMMDD-XXXX or INV-OUT-YYYYMMDD-XXXX
                $parts = explode('-', $lastTransaksi->invoice);
                if (count($parts) === 4) {
                    $lastDate = $parts[2];
                    $lastNumber = intval($parts[3]);
                    
                    // If same date, increment. Otherwise start from 1
                    if ($lastDate === $dateStr) {
                        $nextNumber = $lastNumber + 1;
                    }
                }
            }
            
            $invoiceNumber = $prefix . $dateStr . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            
            // Check if invoice exists (race condition protection)
            $exists = PosTransaksi::where('owner_id', $ownerId)
                ->where('invoice', $invoiceNumber)
                ->exists();
            
            if (!$exists) {
                return $invoiceNumber;
            }
            
            $attempt++;
            // Small delay to reduce collision
            usleep(100000); // 0.1 second
        }
        
        // Fallback: use timestamp if all retries failed
        $prefix = $isMasuk ? 'INV-IN-' : 'INV-OUT-';
        return $prefix . date('Ymd-His') . '-' . rand(1000, 9999);
    }
    
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

        // Generate invoice number based on last invoice, not ID
        $lastTransaksi = PosTransaksi::where('owner_id', $ownerId)
            ->orderBy('id', 'desc')
            ->first();
        
        $dateStr = date('Ymd');
        $nextNumber = 1;
        
        if ($lastTransaksi && $lastTransaksi->invoice) {
            // Parse last invoice: INV-YYYYMMDD-XXXX
            $parts = explode('-', $lastTransaksi->invoice);
            if (count($parts) === 3) {
                $lastDate = $parts[1];
                $lastNumber = intval($parts[2]);
                
                // If same date, increment. Otherwise start from 1
                if ($lastDate === $dateStr) {
                    $nextNumber = $lastNumber + 1;
                }
            }
        }
        
        $invoiceNumber = 'INV-' . $dateStr . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

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
        
        // Load products with stock per store
        $produks = PosProduk::where('owner_id', $ownerId)
            ->with(['merk', 'stok' => function($query) use ($ownerId) {
                $query->where('owner_id', $ownerId)
                    ->select('pos_produk_id', 'pos_toko_id', 'stok');
            }])
            ->get()
            ->map(function($produk) {
                // Create stock array per store [toko_id => stok]
                $produk->stok_per_toko = $produk->stok->pluck('stok', 'pos_toko_id')->toArray();
                return $produk;
            });
        
        $services = PosService::where('owner_id', $ownerId)->get();

        // Invoice akan di-generate otomatis saat submit untuk menghindari duplicate
        $invoiceNumber = '';

        return view('pages.transaksi.masuk.create', compact('tokos', 'pelanggans', 'produks', 'services', 'invoiceNumber'));
    }

    public function storeMasuk(Request $request)
    {
        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        // Auto-generate invoice if empty or not provided
        $invoice = $request->invoice;
        if (empty($invoice)) {
            $invoice = $this->generateInvoiceNumber($ownerId, true);
        }

        $request->merge(['invoice' => $invoice]);

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
            'invoice' => $invoice,
            'total_harga' => $request->total_harga,
            'keterangan' => $request->keterangan,
            'status' => $request->status,
            'metode_pembayaran' => $request->metode_pembayaran,
        ]);

        // Process transaction items and update stock if items exist
        if ($request->has('items') && is_array($request->items)) {
            foreach ($request->items as $itemData) {
                // Skip empty items
                if (empty($itemData['item_id']) || empty($itemData['type'])) {
                    continue;
                }

                $pos_produk_id = $itemData['type'] === 'product' ? $itemData['item_id'] : null;
                $pos_service_id = $itemData['type'] === 'service' ? $itemData['item_id'] : null;
                
                // Validate stock for products (incoming = sales, stock should decrease)
                if ($pos_produk_id) {
                    $quantity = $itemData['quantity'] ?? 1;
                    
                    // Get available stock for this product IN THE SELECTED STORE
                    $stokData = \App\Models\ProdukStok::where('owner_id', $ownerId)
                        ->where('pos_toko_id', $request->pos_toko_id)
                        ->where('pos_produk_id', $pos_produk_id)
                        ->first();
                    
                    $availableStock = $stokData ? $stokData->stok : 0;
                    
                    if ($availableStock < $quantity) {
                        $produk = PosProduk::find($pos_produk_id);
                        $productName = $produk ? $produk->nama : 'Product ID ' . $pos_produk_id;
                        
                        return response()->json([
                            'success' => false,
                            'message' => "Insufficient stock for {$productName} in the selected store. Available: {$availableStock}, Requested: {$quantity}"
                        ], 422);
                    }
                }
                
                // Create transaction item
                PosTransaksiItem::create([
                    'pos_transaksi_id' => $transaksi->id,
                    'pos_produk_id' => $pos_produk_id,
                    'pos_service_id' => $pos_service_id,
                    'quantity' => $itemData['quantity'] ?? 1,
                    'harga_satuan' => $itemData['harga_satuan'] ?? 0,
                    'subtotal' => $itemData['subtotal'] ?? 0,
                    'diskon' => $itemData['diskon'] ?? 0,
                    'garansi' => $itemData['garansi'] ?? null,
                    'garansi_expires_at' => $itemData['garansi_expires_at'] ?? null,
                    'pajak' => $itemData['pajak'] ?? 0,
                ]);

                // Update stock for products (not services)
                if ($pos_produk_id) {
                    $quantity = $itemData['quantity'] ?? 1;
                    // Transaksi masuk (sales) = stock out (reduce stock)
                    $this->updateProductStock(
                        $ownerId,
                        $request->pos_toko_id,
                        $pos_produk_id,
                        -$quantity,
                        'keluar',
                        $request->invoice,
                        'Penjualan produk'
                    );
                }
            }
        }

        // Check if request is AJAX
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Incoming transaction has been successfully created',
                'data' => $transaksi,
                'print_url' => route('transaksi.masuk.print', $transaksi->id),
                'redirect_url' => route('transaksi.masuk.index'),
            ]);
        }

        return redirect()->route('transaksi.masuk.index')->with('success', 'Transaksi masuk berhasil ditambahkan');
    }

    public function printMasuk($id)
    {
        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $transaksi = PosTransaksi::where('owner_id', $ownerId)
            ->where('is_transaksi_masuk', 1)
            ->with(['items.produk.merk', 'items.service', 'toko', 'pelanggan'])
            ->findOrFail($id);

        return view('pages.transaksi.masuk.print', compact('transaksi'));
    }

    public function editMasuk($id)
    {
        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $transaksi = PosTransaksi::where('owner_id', $ownerId)
            ->where('is_transaksi_masuk', 1)
            ->with('items.produk.merk', 'items.service')
            ->findOrFail($id);
        
        $tokos = PosToko::where('owner_id', $ownerId)->get();
        $pelanggans = PosPelanggan::where('owner_id', $ownerId)->get();
        $produks = PosProduk::where('owner_id', $ownerId)->with('merk')->get();
        $services = PosService::where('owner_id', $ownerId)->get();

        return view('pages.transaksi.masuk.edit', compact('transaksi', 'tokos', 'pelanggans', 'produks', 'services'));
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

    public function destroyMasuk($id)
    {
        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $transaksi = PosTransaksi::where('owner_id', $ownerId)
            ->where('is_transaksi_masuk', 1)
            ->findOrFail($id);

        $transaksi->delete();

        return redirect()->route('transaksi.masuk.index')->with('success', 'Incoming transaction deleted successfully');
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
        $produks = PosProduk::where('owner_id', $ownerId)->with('merk')->get();
        $services = PosService::where('owner_id', $ownerId)->get();
        $merks = \App\Models\PosProdukMerk::where('owner_id', $ownerId)->get();

        // Invoice akan di-generate otomatis saat submit untuk menghindari duplicate
        $invoiceNumber = '';

        return view('pages.transaksi.keluar.create', compact('tokos', 'suppliers', 'produks', 'services', 'merks', 'invoiceNumber'));
    }

    public function storeKeluar(Request $request)
    {
        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        // Auto-generate invoice if empty or not provided
        $invoice = $request->invoice;
        if (empty($invoice)) {
            $invoice = $this->generateInvoiceNumber($ownerId, false);
        }

        $request->merge(['invoice' => $invoice]);

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
            'invoice' => $invoice,
            'total_harga' => $request->total_harga,
            'keterangan' => $request->keterangan,
            'status' => $request->status,
            'metode_pembayaran' => $request->metode_pembayaran,
        ]);

        // Process transaction items and update stock if items exist
        if ($request->has('items') && is_array($request->items)) {
            foreach ($request->items as $itemData) {
                // Skip empty items
                if (empty($itemData['item_id']) || empty($itemData['type'])) {
                    continue;
                }

                $pos_produk_id = $itemData['type'] === 'product' ? $itemData['item_id'] : null;
                $pos_service_id = $itemData['type'] === 'service' ? $itemData['item_id'] : null;
                
                // Create transaction item
                PosTransaksiItem::create([
                    'pos_transaksi_id' => $transaksi->id,
                    'pos_produk_id' => $pos_produk_id,
                    'pos_service_id' => $pos_service_id,
                    'quantity' => $itemData['quantity'] ?? 1,
                    'harga_satuan' => $itemData['harga_satuan'] ?? 0,
                    'subtotal' => $itemData['subtotal'] ?? 0,
                    'diskon' => $itemData['diskon'] ?? 0,
                    'garansi' => $itemData['garansi'] ?? null,
                    'garansi_expires_at' => $itemData['garansi_expires_at'] ?? null,
                    'pajak' => $itemData['pajak'] ?? 0,
                ]);

                // Update stock for products (not services)
                if ($pos_produk_id) {
                    $quantity = $itemData['quantity'] ?? 1;
                    // Transaksi keluar (purchase) = stock in (add stock)
                    $this->updateProductStock(
                        $ownerId,
                        $request->pos_toko_id,
                        $pos_produk_id,
                        $quantity,
                        'masuk',
                        $request->invoice,
                        'Pembelian produk dari supplier'
                    );
                }
            }
        }

        // Check if request is AJAX
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Outgoing transaction has been successfully created',
                'data' => $transaksi,
                'print_url' => route('transaksi.keluar.print', $transaksi->id),
                'redirect_url' => route('transaksi.keluar.index'),
            ]);
        }

        return redirect()->route('transaksi.keluar.index')->with('success', 'Outgoing transaction has been successfully created');
    }

    public function printKeluar($id)
    {
        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $transaksi = PosTransaksi::where('owner_id', $ownerId)
            ->where('is_transaksi_masuk', 0)
            ->with(['items.produk.merk', 'items.service', 'toko', 'supplier'])
            ->findOrFail($id);

        return view('pages.transaksi.keluar.print', compact('transaksi'));
    }

    public function editKeluar($id)
    {
        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $transaksi = PosTransaksi::where('owner_id', $ownerId)
            ->where('is_transaksi_masuk', 0)
            ->with('items.produk.merk', 'items.service')
            ->findOrFail($id);
        
        $tokos = PosToko::where('owner_id', $ownerId)->get();
        $suppliers = PosSupplier::where('owner_id', $ownerId)->get();
        $produks = PosProduk::where('owner_id', $ownerId)->with('merk')->get();
        $services = PosService::where('owner_id', $ownerId)->get();

        return view('pages.transaksi.keluar.edit', compact('transaksi', 'tokos', 'suppliers', 'produks', 'services'));
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

    public function destroyKeluar($id)
    {
        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $transaksi = PosTransaksi::where('owner_id', $ownerId)
            ->where('is_transaksi_masuk', 0)
            ->findOrFail($id);

        $transaksi->delete();

        return redirect()->route('transaksi.keluar.index')->with('success', 'Outgoing transaction deleted successfully');
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
