<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Carbon\Carbon;
use Illuminate\Support\Str;

class AnalyticsChatService
{
    /**
     * Answer a natural language analytics question (Indonesian) about store and sales.
     * Returns an array with 'answer' string and optional 'data' array for tabular details.
     */
    public function answer(string $query): array
    {
        $q = Str::of(mb_strtolower(trim($query)));

        // Determine time range
        [$start, $end, $rangeLabel] = $this->parseDateRange($q->toString());

        // Metrics routing
        // Revenue / Omzet (support both omzet/omset and common phrasings)
        if ($q->contains(['total penjualan', 'omset', 'omzet', 'pendapatan', 'omset penjualan', 'omzet penjualan'])) {
            $sum = $this->sumRevenue($start, $end);
            return [
                'answer' => sprintf(
                    'Omzet %s: Rp %s',
                    $rangeLabel,
                    number_format($sum, 0, ',', '.')
                ),
            ];
        }

        if ($q->contains(['jumlah transaksi', 'berapa transaksi'])) {
            $count = $this->countTransactions($start, $end);
            return [
                'answer' => sprintf('Jumlah transaksi %s: %d', $rangeLabel, $count),
            ];
        }

        if ($q->contains(['rata-rata', 'average'])) {
            $avg = $this->avgOrderValue($start, $end);
            return [
                'answer' => sprintf(
                    'Rata-rata nilai transaksi %s: Rp %s',
                    $rangeLabel,
                    number_format($avg, 0, ',', '.')
                ),
            ];
        }

        // Top products
        if ($q->contains(['produk terlaris', 'barang terlaris', 'paling laku', 'terlaris'])) {
            $top = $this->topProducts($start, $end, 5);
            if (count($top) === 0) {
                return ['answer' => 'Belum ada penjualan pada periode tersebut.'];
            }
            $lines = [];
            foreach ($top as $i => $row) {
                $lines[] = ($i + 1) . ". {$row['name']} — {$row['quantity']} terjual";
            }
            return [
                'answer' => "Produk terlaris {$rangeLabel}:\n" . implode("\n", $lines),
                'data' => $top,
            ];
        }

        // iPhone stock summary (early catch to avoid generic stock parser capturing the query)
        if ($q->contains('iphone') && $q->contains(['stok', 'produk', 'berapa'])) {
            $iphones = Product::where('name', 'like', '%iphone%')->where('stock', '>', 0)->get();
            if ($iphones->count() === 0) {
                return ['answer' => 'Tidak ada produk iPhone yang tersedia untuk stok.'];
            }
            $lines = [];
            $total = 0;
            foreach ($iphones as $p) {
                $lines[] = $p->name . ' — Stok: ' . (int)$p->stock . ' unit';
                $total += (int)$p->stock;
            }
            $answer = "Produk iPhone yang tersedia untuk stok:\n" . implode("\n", $lines)
                . "\n\nTotal produk iPhone yang bisa distok: " . $iphones->count() . " tipe\nTotal unit iPhone yang tersedia: " . $total . ' unit';
            return ['answer' => $answer];
        }

        // Stock lookup
        if ($q->contains(['stok', 'stock'])) {
            $name = $this->extractProductName($q->toString());
            if ($name) {
                $product = Product::where('name', 'like', "%{$name}%")->first();
                if ($product) {
                    return ['answer' => sprintf('Stok "%s": %d', $product->name, (int)$product->stock)];
                }
                return ['answer' => 'Produk tidak ditemukan.'];
            }
        }

        // Product-specific sales
        if ($q->contains(['penjualan produk', 'terjual']) || ($q->contains('produk') && $q->contains(['terjual','terjual berapa'])) || $q->contains('penjualan')) {
            $name = $this->extractProductName($q->toString());
            if ($name) {
                $data = $this->productSales($name, $start, $end);
                if ($data['quantity'] > 0) {
                    return [
                        'answer' => sprintf(
                            'Penjualan %s untuk "%s": %d unit (Rp %s)',
                            $rangeLabel,
                            $data['name'],
                            $data['quantity'],
                            number_format($data['revenue'], 0, ',', '.')
                        ),
                        'data' => $data,
                    ];
                }
                return ['answer' => 'Belum ada penjualan untuk produk tersebut pada periode ini.'];
            }
        }

        // Profit synonyms
        if ($q->contains(['profit', 'laba', 'keuntungan'])) {
            $profit = $this->estimatedProfit($start, $end);
            return [
                'answer' => sprintf('Estimasi profit %s: Rp %s', $rangeLabel, number_format($profit, 0, ',', '.')),
            ];
        }

        // Customer favorit (top customers by total spending and orders)
        if ($q->contains(['customer favorit', 'pelanggan favorit', 'customer terbaik', 'pelanggan terbaik', 'langganan terbaik'])) {
            $rows = Transaction::query()
                ->join('customers', 'transactions.customer_id', '=', 'customers.id')
                ->when($start && $end, function ($qq) use ($start, $end) {
                    $qq->whereBetween('transactions.date', [$start, $end]);
                })
                ->groupBy('customers.id', 'customers.name')
                ->selectRaw('customers.name as name, COUNT(transactions.id) as orders, SUM(transactions.total_price) as spent')
                ->orderByDesc('spent')
                ->limit(5)
                ->get();

            if ($rows->isEmpty()) {
                return ['answer' => 'Belum ada transaksi pada periode tersebut.'];
            }

            $lines = [];
            foreach ($rows as $i => $r) {
                $lines[] = ($i + 1) . ". {$r->name} — {$r->orders} order, Rp " . number_format((float)$r->spent, 0, ',', '.');
            }
            return [
                'answer' => "Customer favorit {$rangeLabel} (berdasarkan total belanja):\n" . implode("\n", $lines),
                'data' => $rows->map(function ($r) {
                    return [
                        'name' => $r->name,
                        'orders' => (int) $r->orders,
                        'total_spent' => (float) $r->spent,
                    ];
                })->toArray(),
            ];
        }

        // Rekomendasi iPhone: total penjualan dan customer terbanyak
        if ($q->contains(['rekomendasi iphone', 'penjualan iphone', 'customer terbanyak iphone', 'iphone terbanyak'])) {
            // Total iPhone terjual
            $totalIphoneSold = TransactionItem::join('products', 'transaction_items.product_id', '=', 'products.id')
                ->where('products.name', 'like', '%iphone%')
                ->whereBetween('transaction_items.created_at', [$start, $end])
                ->sum('transaction_items.quantity');

            // Customer terbanyak beli iPhone
            $topIphoneCustomers = Transaction::join('transaction_items', 'transactions.id', '=', 'transaction_items.transaction_id')
                ->join('products', 'transaction_items.product_id', '=', 'products.id')
                ->join('customers', 'transactions.customer_id', '=', 'customers.id')
                ->where('products.name', 'like', '%iphone%')
                ->whereBetween('transactions.date', [$start, $end])
                ->select('customers.name', \DB::raw('SUM(transaction_items.quantity) as total_iphone_bought'))
                ->groupBy('customers.name')
                ->orderByDesc('total_iphone_bought')
                ->limit(5)
                ->get();

            $lines = [];
            foreach ($topIphoneCustomers as $i => $row) {
                $lines[] = ($i + 1) . ". {$row->name} ({$row->total_iphone_bought} unit)";
            }
            $answer = "Total iPhone terjual {$rangeLabel}: {$totalIphoneSold} unit.\nCustomer terbanyak membeli iPhone:\n" . implode("\n", $lines);
            return [
                'answer' => $answer,
                'data' => [
                    'total_iphone_sold' => $totalIphoneSold,
                    'top_customers' => $topIphoneCustomers,
                ]
            ];
        }

        // Jawaban stok dan tipe iPhone
        if ($q->contains(['stok iphone', 'produk iphone', 'iphone apa saja', 'berapa produk iphone'])) {
            $iphones = Product::where('name', 'like', '%iphone%')->where('stock', '>', 0)->get();
            if ($iphones->count() === 0) {
                return ['answer' => 'Tidak ada produk iPhone yang tersedia untuk stok.'];
            }
            $lines = [];
            $total = 0;
            foreach ($iphones as $p) {
                $lines[] = "{$p->name} — Stok: {$p->stock} unit";
                $total += $p->stock;
            }
            $answer = "Produk iPhone yang tersedia untuk stok:\n" . implode("\n", $lines) . "\n\nTotal produk iPhone yang bisa distok: {$iphones->count()} tipe\nTotal unit iPhone yang tersedia: {$total} unit";
            return ['answer' => $answer];
        }

        // Rekomendasi stok iPhone bulan depan (sederhana: gunakan penjualan 30 hari terakhir + safety stock 20%)
        if ($q->contains(['rekomendasi', 'stok']) && $q->contains('iphone') && ($q->contains('bulan depan') || $q->contains('next month'))) {
            $baseStart = Carbon::now()->subDays(30)->startOfDay();
            $baseEnd = Carbon::now()->endOfDay();

            // Penjualan iPhone per produk pada 30 hari terakhir
            $sales = TransactionItem::query()
                ->join('products', 'transaction_items.product_id', '=', 'products.id')
                ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
                ->where('products.name', 'like', '%iphone%')
                ->whereBetween('transactions.date', [$baseStart, $baseEnd])
                ->groupBy('products.id', 'products.name', 'products.stock')
                ->selectRaw('products.id, products.name, products.stock, SUM(transaction_items.quantity) as sold')
                ->orderByDesc('sold')
                ->get();

            $totalSold = (int) ($sales->sum('sold'));
            $currentStock = (int) Product::where('name', 'like', '%iphone%')->sum('stock');
            $forecastNextMonth = (int) ceil($totalSold * (30 / max(1, $baseEnd->diffInDays($baseStart))) * 1.2); // scale to 30 days + 20%
            $recommendRestock = max(0, $forecastNextMonth - $currentStock);

            $details = [];
            foreach ($sales as $row) {
                $share = $totalSold > 0 ? ($row->sold / $totalSold) : 0;
                $modelForecast = (int) round($forecastNextMonth * $share);
                $modelRestock = max(0, $modelForecast - (int) $row->stock);
                $details[] = [
                    'name' => $row->name,
                    'sold_last_30d' => (int) $row->sold,
                    'current_stock' => (int) $row->stock,
                    'forecast_next_month' => $modelForecast,
                    'recommend_restock' => $modelRestock,
                ];
            }

            $lines = [
                'Perkiraan permintaan iPhone bulan depan (berdasarkan 30 hari terakhir + safety 20%): ' . $forecastNextMonth . ' unit.',
                'Stok iPhone saat ini: ' . $currentStock . ' unit.',
                'Saran restock bulan depan: ' . $recommendRestock . ' unit (estimasi).'
            ];

            return [
                'answer' => implode("\n", $lines),
                'data' => $details,
            ];
        }

        // Default help
        return [
            'answer' => 'Saya bisa bantu: "omzet bulan ini", "jumlah transaksi hari ini", "produk terlaris minggu ini", "stok produk [nama]", "penjualan [nama] bulan ini", "profit tahun ini", "rekomendasi stok iPhone bulan depan", atau "customer favorit".',
        ];
    }

    private function parseDateRange(string $q): array
    {
        $now = Carbon::now();
        $start = null; $end = null; $label = 'keseluruhan';

        if (str_contains($q, 'hari ini')) {
            $start = $now->copy()->startOfDay();
            $end = $now->copy()->endOfDay();
            $label = 'hari ini';
        } elseif (str_contains($q, 'kemarin')) {
            $start = $now->copy()->subDay()->startOfDay();
            $end = $now->copy()->subDay()->endOfDay();
            $label = 'kemarin';
        } elseif (str_contains($q, 'minggu ini')) {
            $start = $now->copy()->startOfWeek();
            $end = $now->copy()->endOfWeek();
            $label = 'minggu ini';
        } elseif (str_contains($q, 'bulan ini')) {
            $start = $now->copy()->startOfMonth();
            $end = $now->copy()->endOfMonth();
            $label = 'bulan ini';
        } elseif (str_contains($q, 'tahun ini')) {
            $start = $now->copy()->startOfYear();
            $end = $now->copy()->endOfYear();
            $label = 'tahun ini';
        }

        return [$start, $end, $label];
    }

    private function sumRevenue(?Carbon $start, ?Carbon $end): float
    {
        $query = Transaction::query();
        if ($start && $end) {
            $query->whereBetween('date', [$start, $end]);
        }
        return (float) $query->sum('total_price');
    }

    private function countTransactions(?Carbon $start, ?Carbon $end): int
    {
        $query = Transaction::query();
        if ($start && $end) {
            $query->whereBetween('date', [$start, $end]);
        }
        return (int) $query->count('id');
    }

    private function avgOrderValue(?Carbon $start, ?Carbon $end): float
    {
        $query = Transaction::query();
        if ($start && $end) {
            $query->whereBetween('date', [$start, $end]);
        }
        return (float) $query->avg('total_price');
    }

    private function topProducts(?Carbon $start, ?Carbon $end, int $limit = 5): array
    {
        $query = TransactionItem::query()
            ->with('product')
            ->whereNotNull('product_id');
        if ($start && $end) {
            $query->whereHas('transaction', function ($q) use ($start, $end) {
                $q->whereBetween('date', [$start, $end]);
            });
        }
        $rows = $query
            ->selectRaw('product_id, SUM(quantity) as qty')
            ->groupBy('product_id')
            ->orderByDesc('qty')
            ->limit($limit)
            ->get();

        return $rows->map(function ($row) {
            $product = Product::find($row->product_id);
            return [
                'product_id' => $row->product_id,
                'name' => $product?->name ?? 'Unknown',
                'quantity' => (int) $row->qty,
            ];
        })->toArray();
    }

    private function extractProductName(string $q): ?string
    {
        // Try patterns: "produk X", "penjualan produk X", quoted names
        if (preg_match('/produk\s+\"([^\"]+)\"/u', $q, $m)) {
            return trim($m[1]);
        }
        if (preg_match('/produk\s+([a-z0-9\-\s]+)/u', $q, $m)) {
            return trim($m[1]);
        }
        if (preg_match('/\"([^\"]+)\"/u', $q, $m)) {
            return trim($m[1]);
        }
        return null;
    }

    private function productSales(string $nameLike, ?Carbon $start, ?Carbon $end): array
    {
        $product = Product::where('name', 'like', "%{$nameLike}%")->first();
        if (!$product) {
            return ['name' => $nameLike, 'quantity' => 0, 'revenue' => 0.0];
        }
        $query = TransactionItem::query()->where('product_id', $product->id);
        if ($start && $end) {
            $query->whereHas('transaction', function ($q) use ($start, $end) {
                $q->whereBetween('date', [$start, $end]);
            });
        }
        $qty = (int) $query->sum('quantity');
        $revenue = (float) $query->sum('subtotal');

        return ['name' => $product->name, 'quantity' => $qty, 'revenue' => $revenue];
    }

    private function estimatedProfit(?Carbon $start, ?Carbon $end): float
    {
        $query = TransactionItem::query()->with('product')->whereNotNull('product_id');
        if ($start && $end) {
            $query->whereHas('transaction', function ($q) use ($start, $end) {
                $q->whereBetween('date', [$start, $end]);
            });
        }
        $sum = 0.0;
        foreach ($query->get() as $item) {
            $buy = (float) ($item->product->buy_price ?? 0.0);
            $sell = (float) ($item->price_per_item ?? $item->product->sell_price ?? 0.0);
            $sum += max(0, $sell - $buy) * (int) $item->quantity;
        }
        return $sum;
    }
}
