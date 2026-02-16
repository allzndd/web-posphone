<?php

namespace App\Http\Controllers\Api;

use App\Models\PosProduk;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductSearchController extends Controller
{
    /**
     * Search products by nama, imei, or other attributes
     */
    public function search(Request $request)
    {
        $query = $request->input('q', '');
        $limit = $request->input('limit', 10);

        if (empty($query)) {
            return response()->json([
                'success' => true,
                'data' => [],
                'message' => 'No search query provided'
            ]);
        }

        try {
            $user = auth()->user();
            $ownerId = $user->owner ? $user->owner->id : null;

            $products = PosProduk::query()
                ->where('owner_id', $ownerId)
                ->where(function ($q) use ($query) {
                    $q->where('nama', 'like', "%{$query}%")
                        ->orWhere('imei', 'like', "%{$query}%")
                        ->orWhere('slug', 'like', "%{$query}%");
                })
                ->select(
                    'id',
                    'nama',
                    'slug',
                    'imei',
                    'harga_jual',
                    'harga_beli',
                    'warna',
                    'penyimpanan'
                )
                ->limit($limit)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $products->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'nama' => $product->nama,
                        'slug' => $product->slug,
                        'imei' => $product->imei,
                        'harga_jual' => $product->harga_jual,
                        'warna' => $product->warna,
                        'penyimpanan' => $product->penyimpanan,
                        'url' => route('produk.show', $product->slug)
                    ];
                }),
                'count' => $products->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Search error: ' . $e->getMessage()
            ], 500);
        }
    }
}
