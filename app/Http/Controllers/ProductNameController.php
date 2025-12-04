<?php

namespace App\Http\Controllers;

use App\Models\ProductName;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductNameController extends Controller
{
    public function index(Request $request)
    {
        $query = ProductName::query();
        if ($search = $request->get('q')) {
            $query->where('name', 'like', "%{$search}%");
        }
        $names = $query->orderBy('name')->paginate(15)->withQueryString();
        // Compute remaining stock per name (sum of product stock)
        $stocks = Product::selectRaw('name, COALESCE(SUM(stock),0) as total_stock')
            ->groupBy('name')
            ->pluck('total_stock', 'name');
        return view('pages.product-names.index', compact('names', 'stocks'));
    }

    public function create()
    {
        return view('pages.product-names.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:product_names,name',
            'description' => 'nullable|string',
        ]);
        ProductName::create($data);
        return redirect()->route('product-name.index')->with('success', 'Nama produk berhasil ditambahkan.');
    }

    public function edit(ProductName $product_name)
    {
        return view('pages.product-names.edit', ['nameItem' => $product_name]);
    }

    public function update(Request $request, ProductName $product_name)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:product_names,name,' . $product_name->id,
            'description' => 'nullable|string',
        ]);
        $product_name->update($data);
        return redirect()->route('product-name.index')->with('success', 'Nama produk berhasil diupdate.');
    }

    public function destroy(ProductName $product_name)
    {
        // Prevent deletion if used by any products
        $count = Product::where('name', $product_name->name)->count();
        if ($count > 0) {
            return redirect()->route('product-name.index')
                ->with('error', 'Tidak dapat menghapus. Masih ada ' . $count . ' produk yang menggunakan nama ini.');
        }
        $product_name->delete();
        return redirect()->route('product-name.index')->with('success', 'Nama produk dihapus.');
    }
}
