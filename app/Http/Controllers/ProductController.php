<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    //index
    public function index(Request $request)
    {
        $query = \App\Models\Product::with('category');

        if ($request->filled('name')) {
            $search = $request->input('name');
            $query->where('name', 'like', "%{$search}%");
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->input('start_date'));
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->input('end_date'));
        }

        // Jangan hapus produk yang stoknya 0, hanya sembunyikan
        $products = $query->where('stock', '>', 0)->orderByDesc('created_at')->paginate(10)->withQueryString();
        $categories = \App\Models\Category::orderBy('name')->get();

        return view('pages.product.index', compact('products', 'categories'));
    }

    //create
    public function create()
    {
        $productNames = \App\Models\ProductName::orderBy('name')->pluck('name');
        $categories = \App\Models\Category::orderBy('name')->get();
        $storages = \App\Models\Storage::orderBy('name')->get();
        $colors = \App\Models\Color::orderBy('name')->get();
        return view('pages.product.create', compact('productNames', 'categories', 'storages', 'colors'));
    }

    //show - view product details
    public function show(\App\Models\Product $product)
    {
        // Increment view count
        $product->increment('view_count');

        return view('pages.product.show', compact('product'));
    }

    //store
    public function store(Request $request)
    {
        // Validate inputs
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'harga_jual' => 'required|numeric|min:0',
            'harga_beli' => 'required|numeric|min:0',
            'costs' => 'nullable|array',
            'costs.*.description' => 'nullable|string|max:255',
            'costs.*.amount' => 'nullable|numeric|min:0',
            'barre_health' => 'nullable|string|max:10',
            'color' => 'nullable|string|max:100',
            'storage' => 'nullable|string|max:100',
            'stock' => 'required|integer|min:0',
            'imei' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'assessoris' => 'nullable|string',
            'image' => 'nullable|image',
        ]);

        // Ensure a default category exists (for schema constraint)
        $defaultCategoryId = $data['category_id'] ?? (\App\Models\Category::first()->id ?? \App\Models\Category::create([
            'name' => 'General',
            'slug' => 'general'
        ])->id);

        // Handle optional image upload
        $filename = null;
        if ($request->hasFile('image')) {
            $filename = time() . '.' . $request->image->extension();
            $request->image->storeAs('public/products', $filename);
        }

        // Generate unique slug from name
        $baseSlug = Str::slug($data['name']);
        $slug = $baseSlug;
        $i = 1;
        while (\App\Models\Product::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $i++;
        }

        // Compute profit fields
        $sell = (float) $data['harga_jual'];
        $buy = (float) $data['harga_beli'];
        $costs = $data['costs'] ?? [];
        $totalCost = 0;
        foreach ($costs as $item) {
            $totalCost += isset($item['amount']) ? (float)$item['amount'] : 0;
        }
        $gross = $sell - $buy;
        $net = $sell - $totalCost;

        // Persist
        $product = new \App\Models\Product;
        $product->name = $data['name'];
        $product->slug = $slug;
        $product->category_id = $defaultCategoryId;
        $product->sell_price = $sell;
        $product->buy_price = $buy;
        $product->costs = json_encode($costs);
        $product->barre_health = $data['barre_health'] ?? null;
        $product->gross_profit = $gross;
        $product->net_profit = $net;
        $product->description = $data['description'] ?? null;
        $product->assessoris = $data['assessoris'] ?? null;
        $product->imei = $data['imei'] ?? null;
        $product->color = $data['color'] ?? null;
        $product->storage = $data['storage'] ?? null;
        $product->stock = (int) $data['stock'];
        $product->view_count = 0;
        if ($filename) {
            $product->image_url = $filename;
        }
        $product->save();

        return redirect()->route('product.index')->with('success', 'Product created');
    }

    // edit
    public function edit(\App\Models\Product $product)
    {
        $productNames = \App\Models\ProductName::orderBy('name')->pluck('name');
        $categories = \App\Models\Category::orderBy('name')->get();
        $storages = \App\Models\Storage::orderBy('name')->get();
        $colors = \App\Models\Color::orderBy('name')->get();
        return view('pages.product.edit', compact('product', 'productNames', 'categories', 'storages', 'colors'));
    }

    // update
    public function update(Request $request, \App\Models\Product $product)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'harga_jual' => 'required|numeric|min:0',
            'harga_beli' => 'required|numeric|min:0',
            'costs' => 'nullable|array',
            'costs.*.description' => 'nullable|string|max:255',
            'costs.*.amount' => 'nullable|numeric|min:0',
            'barre_health' => 'nullable|string|max:10',
            'color' => 'nullable|string|max:100',
            'storage' => 'nullable|string|max:100',
            'stock' => 'required|integer|min:0',
            'imei' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'assessoris' => 'nullable|string',
            'image' => 'nullable|image',
        ]);

        // Ensure default category id for schema
        $defaultCategoryId = $data['category_id'] ?? (\App\Models\Category::first()->id ?? \App\Models\Category::create([
            'name' => 'General',
            'slug' => 'general'
        ])->id);

        // Optional image upload
        if ($request->hasFile('image')) {
            $filename = time() . '.' . $request->image->extension();
            $request->image->storeAs('public/products', $filename);
            $product->image_url = $filename;
        }

        // Slug: keep existing; if name changes and slug collides, adjust
        if ($product->name !== $data['name']) {
            $baseSlug = Str::slug($data['name']);
            $slug = $baseSlug;
            $i = 1;
            while (\App\Models\Product::where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
                $slug = $baseSlug . '-' . $i++;
            }
            $product->slug = $slug;
        }

        $sell = (float) $data['harga_jual'];
        $buy = (float) $data['harga_beli'];
        $costs = $data['costs'] ?? [];
        $totalCost = 0;
        foreach ($costs as $item) {
            $totalCost += isset($item['amount']) ? (float)$item['amount'] : 0;
        }
        $gross = $sell - $buy;
        $net = $sell - $totalCost;

        $product->name = $data['name'];
        $product->category_id = $defaultCategoryId;
        $product->sell_price = $sell;
        $product->buy_price = $buy;
        $product->costs = json_encode($costs);
        $product->barre_health = $data['barre_health'] ?? null;
        $product->gross_profit = $gross;
        $product->net_profit = $net;
        $product->description = $data['description'] ?? null;
        $product->assessoris = $data['assessoris'] ?? null;
        $product->imei = $data['imei'] ?? null;
        $product->color = $data['color'] ?? null;
        $product->storage = $data['storage'] ?? null;
        $product->stock = (int) $data['stock'];
        $product->save();

        return redirect()->route('product.index')->with('success', 'Product updated');
    }

    // destroy
    public function destroy(\App\Models\Product $product)
    {
        // delete product image if exists
        if (!empty($product->image_url)) {
            Storage::disk('public')->delete('products/' . $product->image_url);
        }

        $product->delete();

        return redirect()->route('product.index')->with('success', 'Product deleted');
    }
}
