<?php

namespace App\Http\Controllers;

use App\Models\PosTransaksi;
use App\Models\PosTransaksiItem;
use App\Models\PosToko;
use App\Models\PosPelanggan;
use App\Models\PosSupplier;
use App\Models\PosProduk;
use App\Models\PosService;
use App\Services\PermissionService;
use App\Traits\UpdatesStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TransaksiController extends Controller
{
    use UpdatesStock;

    /**
     * Process stock update for transaction items based on transaction type
     * Only processes when status is "completed"
     * 
     * @param PosTransaksi $transaksi
     * @param bool $reverse - If true, reverses the stock change (for cancellation)
     * @return void
     */
    private function processStockForTransaction($transaksi, $reverse = false)
    {
        $ownerId = $transaksi->owner_id;
        $tokoId = $transaksi->pos_toko_id;
        $isTransaksiMasuk = $transaksi->is_transaksi_masuk;

        // Load items if not loaded
        if (!$transaksi->relationLoaded('items')) {
            $transaksi->load('items.produk');
        }

        // Group items by brand (merk) for consolidated stock update
        $groupedByBrand = [];

        foreach ($transaksi->items as $item) {
            if (!$item->pos_produk_id) {
                continue;
            }

            $produk = $item->produk ?? PosProduk::find($item->pos_produk_id);
            if (!$produk) {
                continue;
            }

            $merkId = $produk->pos_produk_merk_id;
            $quantity = $item->quantity ?? 1;

            if (!isset($groupedByBrand[$merkId])) {
                $groupedByBrand[$merkId] = [
                    'pos_produk_id' => $item->pos_produk_id,
                    'total_quantity' => 0,
                ];
            }

            $groupedByBrand[$merkId]['total_quantity'] += $quantity;
        }

        // Update stock for each brand group
        foreach ($groupedByBrand as $merkId => $data) {
            // Find the PRIMARY (smallest ID) produk for this merk
            $primaryProduk = PosProduk::where('owner_id', $ownerId)
                ->where('pos_produk_merk_id', $merkId)
                ->orderBy('id', 'asc')
                ->first();

            if ($primaryProduk) {
                // Determine stock change direction
                // is_transaksi_masuk = 1 (Sales/Incoming): Stock OUT (negative) when completed, Stock IN when reversed
                // is_transaksi_masuk = 0 (Purchase/Outgoing): Stock IN (positive) when completed, Stock OUT when reversed
                
                if ($isTransaksiMasuk) {
                    // Sales: reduce stock on complete, restore on cancel
                    $stockChange = $reverse ? $data['total_quantity'] : -$data['total_quantity'];
                    $tipe = $reverse ? 'masuk' : 'keluar';
                    $keterangan = $reverse ? 'Pembatalan penjualan - stok dikembalikan' : 'Penjualan produk';
                } else {
                    // Purchase: add stock on complete, remove on cancel
                    $stockChange = $reverse ? -$data['total_quantity'] : $data['total_quantity'];
                    $tipe = $reverse ? 'keluar' : 'masuk';
                    $keterangan = $reverse ? 'Pembatalan pembelian - stok dikurangi' : 'Pembelian produk dari supplier';
                }

                $this->updateProductStock(
                    $ownerId,
                    $tokoId,
                    $primaryProduk->id,
                    $stockChange,
                    $tipe,
                    $transaksi->invoice,
                    $keterangan
                );
            }
        }
    }
    
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
        
        // Use different prefix for incoming vs outgoing
        $prefix = $isMasuk ? 'INV-IN-' : 'INV-OUT-';
        $dateStr = date('Ymd');
        
        // Get last transaction of this type only
        $lastTransaksi = PosTransaksi::where('owner_id', $ownerId)
            ->where('is_transaksi_masuk', $isMasuk ? 1 : 0)
            ->where('invoice', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();
        
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
        
        while ($attempt < $maxRetries) {
            $invoiceNumber = $prefix . $dateStr . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            
            // Check if invoice exists for this owner (race condition protection)
            $exists = PosTransaksi::where('owner_id', $ownerId)
                ->where('invoice', $invoiceNumber)
                ->exists();
            
            if (!$exists) {
                return $invoiceNumber;
            }
            
            // If exists, try next number
            $nextNumber++;
            $attempt++;
            usleep(50000); // 0.05 second delay
        }
        
        // Fallback: use timestamp if all retries failed
        return $prefix . date('Ymd-His') . '-' . rand(1000, 9999);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Check read permission
        $hasAccessRead = PermissionService::check('transaksi.read');

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

        return view('pages.transaksi.index', compact('transaksi', 'hasAccessRead'));
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
            'invoice' => ['required', 'string', 'max:45', Rule::unique('pos_transaksi', 'invoice')->where('owner_id', $ownerId)],
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
            'invoice' => ['required', 'string', 'max:45', Rule::unique('pos_transaksi', 'invoice')->ignore($id)->where('owner_id', $ownerId)],
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
        // Check read permission
        $hasAccessRead = PermissionService::check('transaksi.masuk.read');

        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $transaksi = PosTransaksi::where('owner_id', $ownerId)
            ->where('is_transaksi_masuk', 1)
            ->with(['toko', 'pelanggan'])
            ->when($request->input('search'), function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('invoice', 'like', '%' . $search . '%')
                        ->orWhereHas('toko', function ($subQ) use ($search) {
                            $subQ->where('nama', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('pelanggan', function ($subQ) use ($search) {
                            $subQ->where('nama', 'like', '%' . $search . '%');
                        })
                        ->orWhere('keterangan', 'like', '%' . $search . '%')
                        ->orWhere('metode_pembayaran', 'like', '%' . $search . '%');
                });
            })
            ->when($request->input('status'), function ($query, $status) {
                return $query->where('status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 10));

        return view('pages.transaksi.masuk.index', compact('transaksi', 'hasAccessRead'));
    }

    public function createMasuk()
    {
        // Check permission to create
        if (!PermissionService::check('transaksi.masuk.create')) {
            return redirect()->route('transaksi.masuk.index')->with('error', 'Anda tidak memiliki akses untuk membuat transaksi baru.');
        }

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
        // Check permission to create
        if (!PermissionService::check('transaksi.masuk.create')) {
            return redirect()->route('transaksi.masuk.index')->with('error', 'Anda tidak memiliki akses untuk membuat transaksi baru.');
        }

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
            'invoice' => ['required', 'string', 'max:45', Rule::unique('pos_transaksi', 'invoice')->where('owner_id', $ownerId)],
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
            // Validate stock and group items by brand (merk)
            $groupedByBrand = [];
            
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
                
                // Create transaction item (one entry per item for history/audit trail)
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

                // Group products by brand (merk) for consolidated stock update
                if ($pos_produk_id) {
                    $produk = PosProduk::find($pos_produk_id);
                    if ($produk) {
                        $merkId = $produk->pos_produk_merk_id;
                        $quantity = $itemData['quantity'] ?? 1;
                        
                        // Group key: brand/merk ID (same toko for all items in this transaction)
                        // Store only the FIRST product_id (primary), but accumulate quantities
                        if (!isset($groupedByBrand[$merkId])) {
                            $groupedByBrand[$merkId] = [
                                'pos_produk_id' => $pos_produk_id, // Primary product for this brand
                                'total_quantity' => 0,
                            ];
                        }
                        
                        $groupedByBrand[$merkId]['total_quantity'] += $quantity;
                    }
                }
            }
            
            // Update stock ONLY if status is "completed"
            // For pending/cancelled transactions, stock is NOT updated
            if (strtolower($request->status) === 'completed') {
                // Update stock ONLY for the primary product per brand
                // This ensures only 1 produk_stok entry per (toko + brand)
                foreach ($groupedByBrand as $merkId => $data) {
                    // Find the PRIMARY (smallest ID) produk for this merk
                    // This ensures consecutive transactions always update SAME produk_stok entry
                    $primaryProduk = PosProduk::where('owner_id', $ownerId)
                        ->where('pos_produk_merk_id', $merkId)
                        ->orderBy('id', 'asc')
                        ->first();
                    
                    if ($primaryProduk) {
                        // Transaksi masuk (sales) = stock out (reduce stock)
                        $this->updateProductStock(
                            $ownerId,
                            $request->pos_toko_id,
                            $primaryProduk->id, // Use PRIMARY product, not just first in batch
                            -$data['total_quantity'], // Negative for sales (keluar)
                            'keluar',
                            $request->invoice,
                            'Penjualan produk'
                        );
                        
                        // Delete individual products based on quantity sold
                        // Get products to delete (latest first, excluding primary product)
                        $productsToDelete = PosProduk::where('owner_id', $ownerId)
                            ->where('pos_produk_merk_id', $merkId)
                            ->where('id', '!=', $primaryProduk->id) // Don't delete primary
                            ->orderBy('id', 'desc') // Delete newest first
                            ->limit($data['total_quantity'])
                            ->get();
                        
                        $deletedCount = $productsToDelete->count();
                        
                        // Delete the products
                        foreach ($productsToDelete as $prodToDelete) {
                            $prodToDelete->delete();
                        }
                        
                        // If we need to delete more than non-primary products available,
                        // and total_quantity equals all products (including primary),
                        // we should still keep the primary but it will have 0 stock
                        
                        \Log::info("storeMasuk - Deleted {$deletedCount} products for merk {$merkId}, qty sold: {$data['total_quantity']}");
                    }
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
        // Check permission to update
        if (!PermissionService::check('transaksi.masuk.update')) {
            return redirect()->route('transaksi.masuk.index')->with('error', 'Anda tidak memiliki akses untuk mengedit transaksi.');
        }

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
        // Check permission to update
        if (!PermissionService::check('transaksi.masuk.update')) {
            return redirect()->route('transaksi.masuk.index')->with('error', 'Anda tidak memiliki akses untuk mengubah transaksi.');
        }

        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $transaksi = PosTransaksi::where('owner_id', $ownerId)
            ->where('is_transaksi_masuk', 1)
            ->with('items.produk')
            ->findOrFail($id);

        // Store old status for comparison
        $oldStatus = strtolower($transaksi->status);
        $newStatus = strtolower($request->status);
        $oldTokoId = $transaksi->pos_toko_id;

        $request->validate([
            'pos_toko_id' => 'required',
            'invoice' => ['required', 'string', 'max:45', Rule::unique('pos_transaksi', 'invoice')->ignore($id)->where('owner_id', $ownerId)],
            'total_harga' => 'required|numeric|min:0',
            'status' => 'required|string|max:45',
            'metode_pembayaran' => 'required|string|max:45',
            'pos_pelanggan_id' => 'nullable|exists:pos_pelanggan,id',
            'keterangan' => 'nullable|string|max:255',
        ]);

        // Handle item changes if items provided in request (e.g., from API or AJAX)
        $itemsChanged = false;
        if ($request->has('items') && is_array($request->items) && !empty($request->items)) {
            $itemsChanged = true;
            
            // If transaction was completed, reverse stock first for old items
            if ($oldStatus === 'completed') {
                $this->processStockForTransaction($transaksi, true);
            }
            
            // Delete old items
            $transaksi->items()->delete();
            
            // Create new items and group by brand for stock update
            $groupedByBrand = [];
            foreach ($request->items as $itemData) {
                if (empty($itemData['item_id']) || empty($itemData['type'])) {
                    continue;
                }
                
                $pos_produk_id = $itemData['type'] === 'product' ? $itemData['item_id'] : null;
                $pos_service_id = $itemData['type'] === 'service' ? $itemData['item_id'] : null;
                $quantity = (int)($itemData['quantity'] ?? 1);
                
                PosTransaksiItem::create([
                    'pos_transaksi_id' => $transaksi->id,
                    'pos_produk_id' => $pos_produk_id,
                    'pos_service_id' => $pos_service_id,
                    'quantity' => $quantity,
                    'harga_satuan' => $itemData['harga_satuan'] ?? 0,
                    'subtotal' => $itemData['subtotal'] ?? 0,
                    'diskon' => $itemData['diskon'] ?? 0,
                    'garansi' => $itemData['garansi'] ?? null,
                    'garansi_expires_at' => $itemData['garansi_expires_at'] ?? null,
                    'pajak' => $itemData['pajak'] ?? 0,
                ]);
                
                if ($pos_produk_id) {
                    $produk = PosProduk::find($pos_produk_id);
                    if ($produk) {
                        $merkId = $produk->pos_produk_merk_id;
                        if (!isset($groupedByBrand[$merkId])) {
                            $groupedByBrand[$merkId] = [
                                'pos_produk_id' => $pos_produk_id,
                                'total_quantity' => 0,
                            ];
                        }
                        $groupedByBrand[$merkId]['total_quantity'] += $quantity;
                    }
                }
            }
            
            // If old status was completed or new status is completed, recalculate stock
            if ($oldStatus === 'completed' || $newStatus === 'completed') {
                foreach ($groupedByBrand as $merkId => $data) {
                    $primaryProduk = PosProduk::where('owner_id', $ownerId)
                        ->where('pos_produk_merk_id', $merkId)
                        ->orderBy('id', 'asc')
                        ->first();
                    
                    if ($primaryProduk) {
                        // Apply stock for new items at current toko (use request toko in case it changed)
                        $this->updateProductStock(
                            $ownerId,
                            $request->pos_toko_id,
                            $primaryProduk->id,
                            -$data['total_quantity'],
                            'keluar',
                            $transaksi->invoice,
                            'Penjualan produk (updated)'
                        );
                    }
                }
            }
            
            // Reload transaksi to reflect new items
            $transaksi->load('items.produk');
        }

        // Handle stock changes based on status transition (ONLY if items didn't change)
        // Case 1: pending/cancelled → completed = Reduce stock (process transaction)
        // Case 2: completed → cancelled = Return stock (reverse transaction)
        // Case 3: completed → pending = Return stock (reverse transaction)
        // Case 4: pending ↔ cancelled = No stock change (neither had stock impact)
        if (!$itemsChanged && $oldStatus !== $newStatus) {
            \Log::info('DEBUG updateMasuk - status transition detected:', [
                'oldStatus' => $oldStatus,
                'newStatus' => $newStatus,
                'itemsChanged' => $itemsChanged,
            ]);
            
            if ($oldStatus !== 'completed' && $newStatus === 'completed') {
                // Status changed TO completed - reduce stock (sales = stock out)
                \Log::info('DEBUG updateMasuk - CALLING processStockForTransaction with reverse=false (REDUCE stock)');
                $this->processStockForTransaction($transaksi, false);
            } elseif ($oldStatus === 'completed' && $newStatus !== 'completed') {
                // Status changed FROM completed - return stock (reverse the reduction)
                \Log::info('DEBUG updateMasuk - CALLING processStockForTransaction with reverse=true (RETURN stock)');
                $this->processStockForTransaction($transaksi, true);
            }
        } else {
            \Log::info('DEBUG updateMasuk - NO status transition or items changed:', [
                'itemsChanged' => $itemsChanged,
                'oldStatus !== newStatus' => ($oldStatus !== $newStatus),
            ]);
        }

        // Update transaksi data
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
        // Check permission to delete
        if (!PermissionService::check('transaksi.masuk.delete')) {
            return redirect()->route('transaksi.masuk.index')->with('error', 'Anda tidak memiliki akses untuk menghapus transaksi.');
        }

        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $transaksi = PosTransaksi::where('owner_id', $ownerId)
            ->where('is_transaksi_masuk', 1)
            ->with('items.produk')
            ->findOrFail($id);

        // Reverse stock if transaction was completed
        if (strtolower($transaksi->status) === 'completed') {
            $this->processStockForTransaction($transaksi, true);
        }

        $transaksi->delete();

        return redirect()->route('transaksi.masuk.index')->with('success', 'Transaksi masuk berhasil dihapus');
    }

    /**
     * Bulk delete incoming transactions with stock reversal for completed items
     */
    public function bulkDestroyMasuk(Request $request)
    {
        // Check permission to delete
        if (!PermissionService::check('transaksi.masuk.delete')) {
            return redirect()->route('transaksi.masuk.index')->with('error', 'Anda tidak memiliki akses untuk menghapus transaksi.');
        }

        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $ids = json_decode($request->input('ids'), true);
        
        if (!is_array($ids) || empty($ids)) {
            return redirect()->route('transaksi.masuk.index')->with('error', 'Tidak ada transaksi yang dipilih');
        }

        // Load all transactions to be deleted with their items
        $transaksis = PosTransaksi::where('owner_id', $ownerId)
            ->where('is_transaksi_masuk', 1)
            ->whereIn('id', $ids)
            ->with('items.produk')
            ->get();

        // Reverse stock for all completed transactions
        foreach ($transaksis as $transaksi) {
            if (strtolower($transaksi->status) === 'completed') {
                $this->processStockForTransaction($transaksi, true);
            }
        }

        // Delete transactions
        PosTransaksi::where('owner_id', $ownerId)
            ->where('is_transaksi_masuk', 1)
            ->whereIn('id', $ids)
            ->delete();

        return redirect()->route('transaksi.masuk.index')->with('success', 'Transaksi masuk berhasil dihapus');
    }

    // ============================================
    // OUTGOING TRANSACTIONS (PURCHASES) - is_transaksi_masuk = 0
    // ============================================

    public function indexKeluar(Request $request)
    {
        // Check read permission
        $hasAccessRead = PermissionService::check('transaksi.keluar.read');

        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $transaksi = PosTransaksi::where('owner_id', $ownerId)
            ->where('is_transaksi_masuk', 0)
            ->whereNull('pos_kategori_expense_id') // Exclude expense transactions
            ->with(['toko', 'supplier', 'items.produk'])
            ->when($request->input('search'), function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('invoice', 'like', '%' . $search . '%')
                        ->orWhereHas('toko', function ($subQ) use ($search) {
                            $subQ->where('nama', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('supplier', function ($subQ) use ($search) {
                            $subQ->where('nama', 'like', '%' . $search . '%');
                        })
                        ->orWhere('keterangan', 'like', '%' . $search . '%')
                        ->orWhere('metode_pembayaran', 'like', '%' . $search . '%');
                });
            })
            ->when($request->input('status'), function ($query, $status) {
                return $query->where('status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 10));

        return view('pages.transaksi.keluar.index', compact('transaksi', 'hasAccessRead'));
    }

    public function createKeluar()
    {
        // Check permission to create
        if (!PermissionService::check('transaksi.keluar.create')) {
            return redirect()->route('transaksi.keluar.index')->with('error', 'Anda tidak memiliki akses untuk membuat transaksi baru.');
        }

        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $tokos = PosToko::where('owner_id', $ownerId)->get();
        $suppliers = PosSupplier::where('owner_id', $ownerId)->get();
        $produks = PosProduk::where('owner_id', $ownerId)->with('merk')->get();
        $services = PosService::where('owner_id', $ownerId)->get();
        
        // Get merks with product_type directly from pos_produk_merk table
        // Note: Some data uses auth()->id() (User ID), some uses $user->owner->id (Owner model ID)
        // We need to query both to get all data
        $userId = Auth::id();
        $ownerIds = array_filter([$userId, $ownerId]); // Remove null/empty values and duplicates
        
        $merks = \App\Models\PosProdukMerk::where('is_global', 1)
            ->orWhereIn('owner_id', $ownerIds)
            ->distinct()
            ->orderBy('merk')
            ->orderBy('nama')
            ->get()
            ->map(function($item) {
                // If old data doesn't have product_type, default to 'electronic'
                if (empty($item->product_type)) {
                    $item->product_type = 'electronic';
                }
                return $item;
            })
            ->values();
        
        // Get global colors or owner-specific colors
        $warnas = \App\Models\PosWarna::where(function($query) use ($ownerId) {
            $query->where('is_global', 1)
                  ->orWhere('id_owner', $ownerId);
        })->get();
        
        // Get global RAM or owner-specific RAM
        $rams = \App\Models\PosRam::where(function($query) use ($ownerId) {
            $query->where('is_global', 1)
                  ->orWhere('id_owner', $ownerId);
        })->get();
        
        // Get global storage or owner-specific storage (note: field is id_global not is_global)
        $penyimpanans = \App\Models\PosPenyimpanan::where(function($query) use ($ownerId) {
            $query->where('id_global', 1)
                  ->orWhere('id_owner', $ownerId);
        })->get();

        // Invoice akan di-generate otomatis saat submit untuk menghindari duplicate
        $invoiceNumber = '';

        return view('pages.transaksi.keluar.create', compact('tokos', 'suppliers', 'produks', 'services', 'merks', 'warnas', 'rams', 'penyimpanans', 'invoiceNumber'));
    }

    public function storeKeluar(Request $request)
    {
        // Check permission to create
        if (!PermissionService::check('transaksi.keluar.create')) {
            return redirect()->route('transaksi.keluar.index')->with('error', 'Anda tidak memiliki akses untuk membuat transaksi baru.');
        }

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
            'invoice' => ['required', 'string', 'max:45', Rule::unique('pos_transaksi', 'invoice')->where('owner_id', $ownerId)],
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
            \Log::info('storeKeluar - Processing items:', [
                'owner_id' => $ownerId,
                'pos_toko_id' => $request->pos_toko_id,
                'total_items' => count($request->items),
            ]);
            
            // Track all created/used product IDs for stock update
            $productIdsForStock = [];
            
            foreach ($request->items as $index => $itemData) {
                // Skip empty items
                if (empty($itemData['item_id']) || empty($itemData['type'])) {
                    \Log::info("storeKeluar - Item $index skipped - empty item_id or type");
                    continue;
                }

                \Log::info("storeKeluar - Processing item $index:", [
                    'item_id' => $itemData['item_id'],
                    'item_id_type' => gettype($itemData['item_id']),
                    'type' => $itemData['type'],
                    'quantity' => $itemData['quantity'] ?? 1,
                ]);

                $pos_produk_id = $itemData['type'] === 'product' ? $itemData['item_id'] : null;
                $pos_service_id = $itemData['type'] === 'service' ? $itemData['item_id'] : null;
                
                // Convert quantity to integer to ensure proper calculation
                $quantity = (int)($itemData['quantity'] ?? 1);
                
                // Create transaction item (one entry per item for history/audit trail)
                $transaksiItem = PosTransaksiItem::create([
                    'pos_transaksi_id' => $transaksi->id,
                    'pos_produk_id' => $pos_produk_id,
                    'pos_service_id' => $pos_service_id,
                    'quantity' => $quantity,
                    'harga_satuan' => $itemData['harga_satuan'] ?? 0,
                    'subtotal' => $itemData['subtotal'] ?? 0,
                    'diskon' => $itemData['diskon'] ?? 0,
                    'garansi' => $itemData['garansi'] ?? null,
                    'garansi_expires_at' => $itemData['garansi_expires_at'] ?? null,
                    'pajak' => $itemData['pajak'] ?? 0,
                ]);

                // For products, create individual product records based on quantity
                // Both electronic and accessories: each unit = 1 individual product record
                // IMPORTANT: ALWAYS create NEW product records (clone) for EVERY unit
                // This ensures each transaction creates separate products, not reusing existing ones
                if ($pos_produk_id) {
                    $originalProduk = PosProduk::find($pos_produk_id);
                    if ($originalProduk) {
                        // Check if this is a NEW template product (created from quick add modal)
                        // A template has: no stock records AND no OTHER transaction items (exclude current item we just created)
                        $isNewTemplate = !\App\Models\ProdukStok::where('pos_produk_id', $originalProduk->id)->exists()
                            && !PosTransaksiItem::where('pos_produk_id', $originalProduk->id)
                                ->where('id', '!=', $transaksiItem->id)
                                ->exists();
                        
                        // Track first cloned product ID to update transaction item reference
                        $firstClonedId = null;
                        
                        // Create clone for EACH unit (including first unit)
                        // Original product serves as template only, never added to stock directly
                        // This prevents reuse issues when multiple transactions use same template
                        for ($i = 0; $i < $quantity; $i++) {
                            $clonedProduk = $originalProduk->replicate();
                            
                            // Set IMEI based on product type
                            if ($originalProduk->product_type === 'accessories') {
                                // Accessories don't have IMEI
                                $clonedProduk->imei = null;
                            } else {
                                // For electronic, each unit needs unique IMEI
                                // First unit keeps original IMEI, subsequent units get suffix
                                if ($i === 0) {
                                    $clonedProduk->imei = $originalProduk->imei;
                                } else {
                                    $clonedProduk->imei = $originalProduk->imei . '-' . ($i + 1);
                                }
                            }
                            $clonedProduk->save();
                            
                            // Copy biaya tambahan (additional costs) from original to clone
                            // This ensures biaya tambahan is preserved when template is deleted
                            $biayaTambahanItems = \App\Models\PosProdukBiayaTambahan::where('pos_produk_id', $originalProduk->id)->get();
                            
                            \Log::info("=== CLONE #{$i} - COPY BIAYA TAMBAHAN ===", [
                                'original_produk_id' => $originalProduk->id,
                                'cloned_produk_id' => $clonedProduk->id,
                                'biaya_tambahan_count' => $biayaTambahanItems->count(),
                                'items' => $biayaTambahanItems->toArray(),
                            ]);
                            
                            if ($biayaTambahanItems->count() > 0) {
                                foreach ($biayaTambahanItems as $biayaItem) {
                                    try {
                                        // Insert without timestamps - hosting has wrong column types
                                        $inserted = \DB::table('pos_produk_biaya_tambahan')->insert([
                                            'pos_produk_id' => $clonedProduk->id,
                                            'nama' => $biayaItem->nama,
                                            'harga' => $biayaItem->harga,
                                        ]);
                                        
                                        if ($inserted) {
                                            \Log::info('✅ Biaya tambahan copied to clone:', [
                                                'from_produk_id' => $originalProduk->id,
                                                'to_produk_id' => $clonedProduk->id,
                                                'nama' => $biayaItem->nama,
                                                'harga' => $biayaItem->harga,
                                            ]);
                                        } else {
                                            \Log::error('❌ Insert biaya tambahan to clone returned false');
                                        }
                                    } catch (\Exception $e) {
                                        \Log::error('❌ Failed to copy biaya tambahan to clone:', [
                                            'error' => $e->getMessage(),
                                            'from_produk_id' => $originalProduk->id,
                                            'to_produk_id' => $clonedProduk->id,
                                        ]);
                                    }
                                }
                            } else {
                                \Log::warning('⚠️ No biaya tambahan found on template to copy', [
                                    'original_produk_id' => $originalProduk->id,
                                ]);
                            }
                            
                            // Store first clone ID for transaction item reference
                            if ($i === 0) {
                                $firstClonedId = $clonedProduk->id;
                            }
                            
                            $productIdsForStock[] = $clonedProduk->id;
                            
                            \Log::info("storeKeluar - Created product #{$i} for qty:", [
                                'original_id' => $originalProduk->id,
                                'cloned_id' => $clonedProduk->id,
                                'product_type' => $originalProduk->product_type,
                                'unit_number' => $i + 1,
                                'total_qty' => $quantity,
                                'biaya_tambahan_copied' => $biayaTambahanItems->count(),
                            ]);
                        }
                        
                        // Update transaction item to reference first clone instead of template
                        // This ensures IMEI and product_type display correctly in index
                        if ($firstClonedId && $isNewTemplate) {
                            $transaksiItem->update(['pos_produk_id' => $firstClonedId]);
                        }
                        
                        // Delete template product after cloning if it was a NEW template
                        // This prevents template from appearing in product listing
                        // Only delete if: it was a template (no prior stock/transactions) AND we created clones
                        if ($isNewTemplate && $quantity > 0) {
                            \Log::info("storeKeluar - Deleting template product:", [
                                'template_id' => $originalProduk->id,
                                'clones_created' => $quantity,
                                'is_template' => true,
                            ]);
                            $originalProduk->delete();
                        }
                    }
                }
            }
            
            // Update stock ONLY if status is "completed"
            // For pending/cancelled transactions, stock is NOT updated
            if (strtolower($request->status) === 'completed') {
                // Group products by MERK for consolidated stock update
                // This ensures 1 produk_stok entry per MERK, stok = total units
                $groupedByMerk = [];
                
                foreach ($productIdsForStock as $produkId) {
                    $produk = PosProduk::find($produkId);
                    if ($produk) {
                        $merkId = $produk->pos_produk_merk_id;
                        if (!isset($groupedByMerk[$merkId])) {
                            $groupedByMerk[$merkId] = 0;
                        }
                        $groupedByMerk[$merkId]++;
                    }
                }
                
                // Update stock for PRIMARY product per MERK (total qty for that merk)
                foreach ($groupedByMerk as $merkId => $totalQty) {
                    // Find the PRIMARY (smallest ID) produk for this merk
                    $primaryProduk = PosProduk::where('owner_id', $ownerId)
                        ->where('pos_produk_merk_id', $merkId)
                        ->orderBy('id', 'asc')
                        ->first();
                    
                    if ($primaryProduk) {
                        // Transaksi keluar (purchase from supplier) = stock in (add stock)
                        $this->updateProductStock(
                            $ownerId,
                            $request->pos_toko_id,
                            $primaryProduk->id,
                            $totalQty, // Total units for this merk
                            'masuk',
                            $request->invoice,
                            'Pembelian produk dari supplier'
                        );
                    }
                }
            }
        }

        // Fetch created items with relations
        $items = $transaksi->items()->with(['produk.merk', 'service'])->get();

        // Check if request is AJAX
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Outgoing transaction has been successfully created',
                'data' => $transaksi,
                'items' => $items,
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
        // Check permission to update
        if (!PermissionService::check('transaksi.keluar.update')) {
            return redirect()->route('transaksi.keluar.index')->with('error', 'Anda tidak memiliki akses untuk mengedit transaksi.');
        }

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
        // Check permission to update
        if (!PermissionService::check('transaksi.keluar.update')) {
            return redirect()->route('transaksi.keluar.index')->with('error', 'Anda tidak memiliki akses untuk mengubah transaksi.');
        }

        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $transaksi = PosTransaksi::where('owner_id', $ownerId)
            ->where('is_transaksi_masuk', 0)
            ->with('items.produk')
            ->findOrFail($id);

        // Store old status for comparison
        $oldStatus = strtolower(trim($transaksi->status));
        $newStatus = strtolower(trim($request->status));
        $oldTokoId = $transaksi->pos_toko_id;

        \Log::info('DEBUG updateKeluar start:', [
            'transaksi_id' => $id,
            'oldStatus (trimmed+lower)' => $oldStatus,
            'newStatus (trimmed+lower)' => $newStatus,
            'status_changed' => ($oldStatus !== $newStatus),
            'transaksi_status_raw' => $transaksi->status,
            'request_status_raw' => $request->status,
        ]);

        $request->validate([
            'pos_toko_id' => 'required',
            'invoice' => ['required', 'string', 'max:45', Rule::unique('pos_transaksi', 'invoice')->ignore($id)->where('owner_id', $ownerId)],
            'total_harga' => 'required|numeric|min:0',
            'status' => 'required|string|max:45',
            'metode_pembayaran' => 'required|string|max:45',
            'pos_supplier_id' => 'nullable|exists:pos_supplier,id',
            'keterangan' => 'nullable|string|max:255',
        ]);

        // Handle item changes if items provided in request (e.g., from API or AJAX)
        $itemsChanged = false;
        if ($request->has('items') && is_array($request->items) && !empty($request->items)) {
            $itemsChanged = true;
            
            // If transaction was completed, reverse stock first for old items
            if ($oldStatus === 'completed') {
                $this->processStockForTransaction($transaksi, true);
            }
            
            // Delete old items
            $transaksi->items()->delete();
            
            // Create new items and group by brand for stock update
            $groupedByBrand = [];
            foreach ($request->items as $itemData) {
                if (empty($itemData['item_id']) || empty($itemData['type'])) {
                    continue;
                }
                
                $pos_produk_id = $itemData['type'] === 'product' ? $itemData['item_id'] : null;
                $pos_service_id = $itemData['type'] === 'service' ? $itemData['item_id'] : null;
                $quantity = (int)($itemData['quantity'] ?? 1);
                
                PosTransaksiItem::create([
                    'pos_transaksi_id' => $transaksi->id,
                    'pos_produk_id' => $pos_produk_id,
                    'pos_service_id' => $pos_service_id,
                    'quantity' => $quantity,
                    'harga_satuan' => $itemData['harga_satuan'] ?? 0,
                    'subtotal' => $itemData['subtotal'] ?? 0,
                    'diskon' => $itemData['diskon'] ?? 0,
                    'garansi' => $itemData['garansi'] ?? null,
                    'garansi_expires_at' => $itemData['garansi_expires_at'] ?? null,
                    'pajak' => $itemData['pajak'] ?? 0,
                ]);
                
                if ($pos_produk_id) {
                    $produk = PosProduk::find($pos_produk_id);
                    if ($produk) {
                        $merkId = $produk->pos_produk_merk_id;
                        if (!isset($groupedByBrand[$merkId])) {
                            $groupedByBrand[$merkId] = [
                                'pos_produk_id' => $pos_produk_id,
                                'total_quantity' => 0,
                            ];
                        }
                        $groupedByBrand[$merkId]['total_quantity'] += $quantity;
                    }
                }
            }
            
            // If old status was completed or new status is completed, recalculate stock
            if ($oldStatus === 'completed' || $newStatus === 'completed') {
                foreach ($groupedByBrand as $merkId => $data) {
                    $primaryProduk = PosProduk::where('owner_id', $ownerId)
                        ->where('pos_produk_merk_id', $merkId)
                        ->orderBy('id', 'asc')
                        ->first();
                    
                    if ($primaryProduk) {
                        // Apply stock for new items at current toko (use request toko in case it changed)
                        $this->updateProductStock(
                            $ownerId,
                            $request->pos_toko_id,
                            $primaryProduk->id,
                            $data['total_quantity'],
                            'masuk',
                            $transaksi->invoice,
                            'Pembelian produk dari supplier (updated)'
                        );
                    }
                }
            }
            
            // Reload transaksi to reflect new items
            $transaksi->load('items.produk');
        }

        // Handle stock changes based on status transition (ONLY if items didn't change)
        // Case 1: pending/cancelled → completed = Add stock (process transaction)
        // Case 2: completed → cancelled = Remove stock (reverse transaction)
        // Case 3: completed → pending = Remove stock (reverse transaction)
        // Case 4: pending ↔ cancelled = No stock change (neither had stock impact)
        if (!$itemsChanged && $oldStatus !== $newStatus) {
            \Log::info('DEBUG updateKeluar - status transition detected:', [
                'oldStatus' => $oldStatus,
                'newStatus' => $newStatus,
                'itemsChanged' => $itemsChanged,
            ]);
            
            if ($oldStatus !== 'completed' && $newStatus === 'completed') {
                // Status changed TO completed - add stock (purchase = stock in)
                \Log::info('DEBUG updateKeluar - CALLING processStockForTransaction with reverse=false (ADD stock)');
                $this->processStockForTransaction($transaksi, false);
            } elseif ($oldStatus === 'completed' && $newStatus !== 'completed') {
                // Status changed FROM completed - remove stock (reverse the addition)
                \Log::info('DEBUG updateKeluar - CALLING processStockForTransaction with reverse=true (REMOVE stock)');
                $this->processStockForTransaction($transaksi, true);
            }
        } else {
            \Log::info('DEBUG updateKeluar - NO status transition or items changed:', [
                'itemsChanged' => $itemsChanged,
                'oldStatus !== newStatus' => ($oldStatus !== $newStatus),
            ]);
        }

        // Update transaksi data
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
        // Check permission to delete
        if (!PermissionService::check('transaksi.keluar.delete')) {
            return redirect()->route('transaksi.keluar.index')->with('error', 'Anda tidak memiliki akses untuk menghapus transaksi.');
        }

        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $transaksi = PosTransaksi::where('owner_id', $ownerId)
            ->where('is_transaksi_masuk', 0)
            ->with('items.produk')
            ->findOrFail($id);

        // Reverse stock if transaction was completed
        if (strtolower($transaksi->status) === 'completed') {
            $this->processStockForTransaction($transaksi, true);
        }

        $transaksi->delete();

        return redirect()->route('transaksi.keluar.index')->with('success', 'Outgoing transaction deleted successfully');
    }

    /**
     * Bulk delete outgoing transactions with stock reversal for completed items
     */
    public function bulkDestroyKeluar(Request $request)
    {
        // Check permission to delete
        if (!PermissionService::check('transaksi.keluar.delete')) {
            return redirect()->route('transaksi.keluar.index')->with('error', 'Anda tidak memiliki akses untuk menghapus transaksi.');
        }

        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $ids = json_decode($request->input('ids'), true);
        
        if (!is_array($ids) || empty($ids)) {
            return redirect()->route('transaksi.keluar.index')->with('error', 'No transactions selected');
        }

        // Load all transactions to be deleted with their items
        $transaksis = PosTransaksi::where('owner_id', $ownerId)
            ->where('is_transaksi_masuk', 0)
            ->whereIn('id', $ids)
            ->with('items.produk')
            ->get();

        // Reverse stock for all completed transactions
        foreach ($transaksis as $transaksi) {
            if (strtolower($transaksi->status) === 'completed') {
                $this->processStockForTransaction($transaksi, true);
            }
        }

        // Delete transactions
        PosTransaksi::where('owner_id', $ownerId)
            ->where('is_transaksi_masuk', 0)
            ->whereIn('id', $ids)
            ->delete();

        return redirect()->route('transaksi.keluar.index')->with('success', 'Outgoing transactions deleted successfully');
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
            
            // Summary - only count COMPLETED transactions for financial calculations
            $completedTransaksi = $transaksi->filter(function($t) {
                return strtolower($t->status) === 'completed';
            });
            $totalIncome = $completedTransaksi->where('is_transaksi_masuk', 1)->sum('total_harga');
            $totalExpense = $completedTransaksi->where('is_transaksi_masuk', 0)->sum('total_harga');
            
            fputcsv($file, ['SUMMARY (Completed Transactions Only)', '', '', '', '', '', '', '', '']);
            fputcsv($file, ['Total Transactions', $transaksi->count(), '', '', '', '', '', '', '']);
            fputcsv($file, ['Completed Transactions', $completedTransaksi->count(), '', '', '', '', '', '', '']);
            fputcsv($file, ['Total Income', 'Rp ' . number_format($totalIncome, 0, ',', '.'), '', '', '', '', '', '', '']);
            fputcsv($file, ['Total Expense', 'Rp ' . number_format($totalExpense, 0, ',', '.'), '', '', '', '', '', '', '']);
            fputcsv($file, ['Net Profit', 'Rp ' . number_format($totalIncome - $totalExpense, 0, ',', '.'), '', '', '', '', '', '', '']);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Update status of an incoming transaction via AJAX
     */
    public function updateStatusMasuk(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,completed,cancelled',
        ]);

        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $transaksi = PosTransaksi::where('owner_id', $ownerId)
            ->where('is_transaksi_masuk', 1)
            ->findOrFail($id);

        $oldStatus = strtolower(trim($transaksi->status));
        $newStatus = strtolower(trim($request->status));

        DB::beginTransaction();
        try {
            // If changing TO completed, process stock
            if ($newStatus === 'completed' && $oldStatus !== 'completed') {
                $transaksi->update([
                    'status' => $newStatus,
                    'payment_status' => 'paid',
                    'paid_amount' => $transaksi->total_harga,
                ]);
                $this->processStockForTransaction($transaksi);
            }
            // If changing FROM completed, reverse stock
            elseif ($oldStatus === 'completed' && $newStatus !== 'completed') {
                $this->processStockForTransaction($transaksi, true);
                $transaksi->update([
                    'status' => $newStatus,
                    'payment_status' => 'unpaid',
                    'paid_amount' => 0,
                ]);
            }
            // Otherwise just update status
            else {
                $transaksi->update([
                    'status' => $newStatus,
                ]);
            }

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Status berhasil diubah menjadi ' . ucfirst($newStatus),
                    'status' => $newStatus,
                ]);
            }

            return redirect()->back()->with('success', 'Status berhasil diubah menjadi ' . ucfirst($newStatus));
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengubah status: ' . $e->getMessage(),
                ], 500);
            }
            return redirect()->back()->with('error', 'Gagal mengubah status: ' . $e->getMessage());
        }
    }

    /**
     * Update status of an outgoing transaction via AJAX
     */
    public function updateStatusKeluar(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,completed,cancelled',
        ]);

        $user = Auth::user();
        $ownerId = $user->owner ? $user->owner->id : null;

        $transaksi = PosTransaksi::where('owner_id', $ownerId)
            ->where('is_transaksi_masuk', 0)
            ->whereNull('pos_kategori_expense_id')
            ->findOrFail($id);

        $oldStatus = strtolower(trim($transaksi->status));
        $newStatus = strtolower(trim($request->status));

        DB::beginTransaction();
        try {
            // If changing TO completed, process stock
            if ($newStatus === 'completed' && $oldStatus !== 'completed') {
                $transaksi->update([
                    'status' => $newStatus,
                    'payment_status' => 'paid',
                    'paid_amount' => $transaksi->total_harga,
                ]);
                $this->processStockForTransaction($transaksi);
            }
            // If changing FROM completed, reverse stock
            elseif ($oldStatus === 'completed' && $newStatus !== 'completed') {
                $this->processStockForTransaction($transaksi, true);
                $transaksi->update([
                    'status' => $newStatus,
                    'payment_status' => 'unpaid',
                    'paid_amount' => 0,
                ]);
            }
            // Otherwise just update status
            else {
                $transaksi->update([
                    'status' => $newStatus,
                ]);
            }

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Status berhasil diubah menjadi ' . ucfirst($newStatus),
                    'status' => $newStatus,
                ]);
            }

            return redirect()->back()->with('success', 'Status berhasil diubah menjadi ' . ucfirst($newStatus));
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengubah status: ' . $e->getMessage(),
                ], 500);
            }
            return redirect()->back()->with('error', 'Gagal mengubah status: ' . $e->getMessage());
        }
    }
}
