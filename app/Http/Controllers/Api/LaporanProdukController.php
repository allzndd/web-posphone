<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PosProduk;
use Illuminate\Http\Request;

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
            $query = PosProduk::where('owner_id', $ownerId)
                ->with(['merk', 'stok.toko'])
                ->orderBy('created_at', 'desc');

            if ($request->filled('pos_produk_merk_id')) {
                $query->where('pos_produk_merk_id', $request->pos_produk_merk_id);
            }
            if ($request->filled('nama')) {
                $query->where('nama', 'like', '%' . $request->nama . '%');
            }

            $products = $query->paginate($perPage);
            $products->getCollection()->transform(function ($product) {
                $product->total_stok = $product->stok->sum('stok');
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
}
