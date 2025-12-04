<?php
// Quick test script to verify API works
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Test the search
$search = 'iphone';
$products = App\Models\Product::query()
    ->where('name', 'like', "%{$search}%")
    ->orWhere('slug', 'like', "%{$search}%")
    ->orWhere('imei', 'like', "%{$search}%")
    ->select('id', 'name', 'imei', 'stock', 'sell_price')
    ->orderBy('name')
    ->limit(10)
    ->get();

echo "Search term: {$search}\n";
echo "Found: " . $products->count() . " products\n\n";

foreach ($products as $p) {
    echo "ID: {$p->id}\n";
    echo "Name: {$p->name}\n";
    echo "IMEI: {$p->imei}\n";
    echo "Stock: {$p->stock}\n";
    echo "Price: {$p->sell_price}\n";
    echo "---\n";
}

// Show the JSON response
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

echo "\nJSON Response:\n";
echo json_encode($payload, JSON_PRETTY_PRINT);
