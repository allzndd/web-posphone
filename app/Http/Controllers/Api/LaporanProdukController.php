<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PosProduk;
use App\Models\PosToko;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductsReportExport;

class LaporanProdukController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            $ownerId = $user->owner ? $user->owner->id : null;

            if (!$ownerId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Owner tidak ditemukan',
                ], 403);
            }

            $perPage = $request->get('per_page', 10);
            $tokoId = $request->filled('pos_toko_id') ? $request->pos_toko_id : null;
            $stockStatus = $request->filled('stock_status') ? $request->stock_status : null;
            
            $query = PosProduk::where('owner_id', $ownerId)
                ->with(['merk', 'stok.toko'])
                ->orderBy('created_at', 'desc');

            // Filter by brand
            if ($request->filled('pos_produk_merk_id')) {
                $query->where('pos_produk_merk_id', $request->pos_produk_merk_id);
            }

            // Search by name or slug
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('nama', 'LIKE', "%{$search}%")
                      ->orWhere('slug', 'LIKE', "%{$search}%");
                });
            }

            // If stock status filter, get all and filter in memory
            if ($stockStatus) {
                $allProducts = $query->get();
                
                // Calculate stock and filter
                $filtered = $allProducts->filter(function ($product) use ($tokoId, $stockStatus) {
                    if ($tokoId) {
                        $stock = $product->stok->where('pos_toko_id', $tokoId)->sum('stok');
                    } else {
                        $stock = $product->stok->sum('stok');
                    }
                    
                    $product->total_stok = $stock;
                    
                    if ($stockStatus === 'out_of_stock') {
                        return $stock == 0;
                    } elseif ($stockStatus === 'low_stock') {
                        return $stock > 0 && $stock <= 10;
                    } elseif ($stockStatus === 'in_stock') {
                        return $stock > 10;
                    }
                    return true;
                });
                
                // Manual pagination
                $page = $request->get('page', 1);
                $total = $filtered->count();
                $lastPage = ceil($total / $perPage);
                $offset = ($page - 1) * $perPage;
                $items = $filtered->slice($offset, $perPage)->values();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Laporan produk berhasil diambil',
                    'data' => $items,
                    'pagination' => [
                        'current_page' => (int) $page,
                        'per_page' => (int) $perPage,
                        'total' => $total,
                        'last_page' => (int) $lastPage,
                    ],
                ], 200);
            }

            // Normal pagination without stock filter
            $products = $query->paginate($perPage);
            
            // Add total_stok to each product
            $products->getCollection()->transform(function ($product) use ($tokoId) {
                if ($tokoId) {
                    $product->total_stok = $product->stok->where('pos_toko_id', $tokoId)->sum('stok');
                } else {
                    $product->total_stok = $product->stok->sum('stok');
                }
                return $product;
            });

            return response()->json([
                'success' => true,
                'message' => 'Laporan produk berhasil diambil',
                'data' => $products->items(),
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                    'last_page' => $products->lastPage(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil laporan produk: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getSummary(Request $request)
    {
        try {
            $user = $request->user();
            $ownerId = $user->owner ? $user->owner->id : null;

            if (!$ownerId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Owner tidak ditemukan',
                ], 403);
            }

            $tokoId = $request->filled('pos_toko_id') ? $request->pos_toko_id : null;
            $stockStatus = $request->filled('stock_status') ? $request->stock_status : null;
            
            $query = PosProduk::where('owner_id', $ownerId)
                ->with(['merk', 'stok.toko']);

            // Filter by brand
            if ($request->filled('pos_produk_merk_id')) {
                $query->where('pos_produk_merk_id', $request->pos_produk_merk_id);
            }

            // Search by name or slug
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('nama', 'LIKE', "%{$search}%")
                      ->orWhere('slug', 'LIKE', "%{$search}%");
                });
            }

            $products = $query->get();

            $totalProducts = 0;
            $totalStock = 0;
            $totalValue = 0;
            $outOfStock = 0;
            $lowStock = 0;
            
            // Debug array
            $debugProducts = [];

            foreach ($products as $product) {
                if ($tokoId) {
                    $stock = $product->stok->where('pos_toko_id', $tokoId)->sum('stok');
                } else {
                    $stock = $product->stok->sum('stok');
                }
                
                // Apply stock status filter
                if ($stockStatus) {
                    if ($stockStatus === 'out_of_stock' && $stock != 0) {
                        continue;
                    } elseif ($stockStatus === 'low_stock' && ($stock == 0 || $stock > 10)) {
                        continue;
                    } elseif ($stockStatus === 'in_stock' && $stock <= 10) {
                        continue;
                    }
                }
                
                $totalProducts++;
                $totalStock += $stock;
                // Use harga_jual, fallback to harga_beli if not available
                $hargaJual = $product->harga_jual;
                $hargaBeli = $product->harga_beli;
                $price = $hargaJual ?? $hargaBeli ?? 0;
                $totalValue += $stock * $price;
                
                // Debug info
                $debugProducts[] = [
                    'nama' => $product->nama,
                    'stock' => $stock,
                    'harga_jual_raw' => $product->harga_jual,
                    'harga_beli_raw' => $product->harga_beli,
                    'harga_jual' => $hargaJual,
                    'harga_beli' => $hargaBeli,
                    'price_used' => $price,
                    'subtotal' => $stock * $price,
                ];
                
                if ($stock == 0) {
                    $outOfStock++;
                } elseif ($stock <= 10) {
                    $lowStock++;
                }
            }

            $averageStock = $totalProducts > 0 ? $totalStock / $totalProducts : 0;

            return response()->json([
                'success' => true,
                'data' => [
                    'total_products' => $totalProducts,
                    'total_stock' => $totalStock,
                    'total_value' => $totalValue,
                    'average_stock' => round($averageStock, 2),
                    'out_of_stock' => $outOfStock,
                    'low_stock' => $lowStock,
                ],
                'debug' => $debugProducts, // Temporary debug
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil summary produk: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getStores(Request $request)
    {
        try {
            $user = $request->user();
            $ownerId = $user->owner ? $user->owner->id : null;

            if (!$ownerId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Owner tidak ditemukan',
                ], 403);
            }

            $stores = PosToko::where('owner_id', $ownerId)
                ->select('id', 'nama')
                ->orderBy('nama')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $stores,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data toko: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function exportExcel(Request $request)
    {
        try {
            $user = $request->user();
            $ownerId = $user->owner ? $user->owner->id : null;

            if (!$ownerId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Owner tidak ditemukan',
                ], 403);
            }

            $query = PosProduk::where('owner_id', $ownerId)
                ->with(['merk', 'stok.toko']);

            // Apply filters
            if ($request->filled('pos_produk_merk_id')) {
                $query->where('pos_produk_merk_id', $request->pos_produk_merk_id);
            }
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('nama', 'LIKE', "%{$search}%")
                      ->orWhere('kode', 'LIKE', "%{$search}%");
                });
            }

            $products = $query->orderBy('nama')->get();

            // Calculate summary
            $totalProducts = $products->count();
            $totalStock = 0;
            $totalValue = 0;
            $outOfStock = 0;
            $lowStock = 0;

            foreach ($products as $product) {
                $stock = $product->stok->sum('stok');
                $totalStock += $stock;
                // Use harga_jual, fallback to harga_beli if not available
                $price = floatval($product->harga_jual ?? $product->harga_beli ?? 0);
                $totalValue += $stock * $price;
                
                if ($stock == 0) {
                    $outOfStock++;
                } elseif ($stock <= 10) {
                    $lowStock++;
                }
            }

            $averageStock = $totalProducts > 0 ? $totalStock / $totalProducts : 0;

            $summary = [
                'totalProducts' => $totalProducts,
                'totalStock' => $totalStock,
                'totalValue' => $totalValue,
                'averageStock' => $averageStock,
                'outOfStock' => $outOfStock,
                'lowStock' => $lowStock,
            ];

            return Excel::download(
                new ProductsReportExport($products, $summary),
                'product_report_' . date('Y-m-d_His') . '.xlsx'
            );
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal export laporan: ' . $e->getMessage(),
            ], 500);
        }
    }
}