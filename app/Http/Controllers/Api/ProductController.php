<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function search(Request $request)
    {
        $search = $request->get('search');

        $products = Product::query()
            ->where('stock', '>', 0)
            ->when($search, function($query) use ($search) {
                return $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('slug', 'like', "%{$search}%")
                      ->orWhere('imei', 'like', "%{$search}%");
                });
            })
            ->select('id', 'name', 'imei', 'stock', 'sell_price')
            ->orderBy('name')
            ->limit(10)
            ->get();

        // Backward-compatible shape for existing consumers
        $payload = $products->map(function($p) {
            return [
                'id' => $p->id,
                'product_id' => $p->id,
                'name' => $p->name,
                'imei' => $p->imei,
                'stock' => $p->stock,
                'sell_price' => $p->sell_price,
                'selling_price' => $p->sell_price,
            ];
        });

        return response()->json($payload);
    }
}
