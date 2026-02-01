<?php

namespace App\Services;

use App\Models\PosProduk;
use App\Models\ProdukStok;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIChatbotService
{
    private string $apiKey;
    private string $model;
    private string $apiUrl = 'https://api.groq.com/openai/v1/chat/completions';

    public function __construct()
    {
        $this->apiKey = config('services.groq.api_key');
        $this->model = config('services.groq.model', 'llama3-8b-8192');
    }

    /**
     * Main chat handler - alur lengkap
     */
    public function chat(string $message, ?int $ownerId = null): array
    {
        try {
            // Step 1: Klasifikasi intent
            $intent = $this->classifyIntent($message);
            
            // Step 2: Ambil data dari database berdasarkan intent
            $data = $this->fetchDataByIntent($intent, $ownerId, $message);
            
            // Step 3: Generate response dengan context data
            $response = $this->generateResponse($message, $intent, $data);
            
            return [
                'success' => true,
                'intent' => $intent,
                'message' => $response,
                'data' => $data
            ];
        } catch (\Exception $e) {
            Log::error('AI Chatbot Error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => 'Maaf, terjadi kesalahan saat memproses permintaan Anda.',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ];
        }
    }

    /**
     * Step 1: Klasifikasi intent user
     */
    private function classifyIntent(string $message): string
    {
        $prompt = [
            [
                'role' => 'system',
                'content' => "Klasifikasikan intent user ke salah satu kategori berikut:
- LIST_STORES: User menanyakan daftar toko, toko apa saja
- STORE_PRODUCTS: User menanyakan produk di toko, produk apa saja di toko
- STORE_STOCK: User menanyakan stok produk di toko
- TOP_PRODUCT: User menanyakan produk terlaris, best seller, produk populer
- TOP_STORE: User menanyakan toko dengan penjualan terbanyak, toko terbaik, performa toko
- SALES_TODAY: User menanyakan penjualan hari ini, transaksi hari ini
- STOCK_PRODUCT: User menanyakan stok produk, ketersediaan barang
- PRODUCT_SEARCH: User mencari produk tertentu berdasarkan nama/merk
- PRODUCT_PRICE: User menanyakan harga produk
- UNKNOWN: Pertanyaan di luar kategori di atas

Jawab HANYA dengan 1 kata kategori (contoh: TOP_PRODUCT)"
            ],
            [
                'role' => 'user',
                'content' => $message
            ]
        ];

        $response = $this->callGroqAPI($prompt);
        $intent = strtoupper(trim($response));
        
        // Validasi intent
        $validIntents = ['LIST_STORES', 'STORE_PRODUCTS', 'STORE_STOCK', 'TOP_PRODUCT', 'TOP_STORE', 'SALES_TODAY', 'STOCK_PRODUCT', 'PRODUCT_SEARCH', 'PRODUCT_PRICE', 'UNKNOWN'];
        
        return in_array($intent, $validIntents) ? $intent : 'UNKNOWN';
    }

    /**
     * Step 2: Fetch data dari database berdasarkan intent
     */
    private function fetchDataByIntent(string $intent, ?int $ownerId = null, ?string $originalMessage = null): array
    {
        switch ($intent) {
            case 'LIST_STORES':
                return $this->getAllStores($ownerId);
                
            case 'STORE_PRODUCTS':
                $tokoId = $originalMessage ? $this->extractTokoId($originalMessage, $ownerId) : null;
                return $this->getStoreProducts($ownerId, $tokoId);
                
            case 'STORE_STOCK':
                $tokoId = $originalMessage ? $this->extractTokoId($originalMessage, $ownerId) : null;
                return $this->getStoreStock($ownerId, $tokoId);
                
            case 'TOP_PRODUCT':
                return $this->getTopProducts($ownerId);
                
            case 'TOP_STORE':
                return $this->getTopStores($ownerId);
                
            case 'SALES_TODAY':
                return $this->getSalesToday($ownerId);
                
            case 'STOCK_PRODUCT':
                return $this->getStockSummary($ownerId);
                
            case 'PRODUCT_SEARCH':
                return $this->getAllProducts($ownerId);
                
            case 'PRODUCT_PRICE':
                return $this->getProductPrices($ownerId);
                
            default:
                return [];
        }
    }

    /**
     * Step 3: Generate natural language response dengan data
     */
    private function generateResponse(string $userMessage, string $intent, array $data): string
    {
        if ($intent === 'UNKNOWN') {
            return "Maaf, saya belum bisa membantu pertanyaan tersebut. Saya bisa membantu Anda untuk:\n- Cek produk terlaris\n- Lihat penjualan hari ini\n- Cek stok produk\n- Mencari produk\n- Cek harga produk";
        }

        // Handle empty data dengan pesan yang context-aware
        if (empty($data)) {
            switch ($intent) {
                case 'STORE_PRODUCTS':
                    return "Maaf, semua produk di toko yang Anda tanyakan saat ini sedang habis stok. Silahkan coba toko lain atau tanyakan produk terlaris kami.";
                case 'STORE_STOCK':
                    return "Maaf, toko yang Anda tanyakan tidak memiliki produk dengan stok tersedia. Silahkan coba toko lain.";
                case 'LIST_STORES':
                    return "Maaf, tidak ada data toko yang tersedia saat ini.";
                case 'TOP_PRODUCT':
                    return "Belum ada data penjualan produk. Silahkan coba pertanyaan lain.";
                case 'TOP_STORE':
                    return "Belum ada data penjualan toko. Silahkan coba pertanyaan lain.";
                default:
                    return "Maaf, data untuk pertanyaan Anda tidak tersedia saat ini.";
            }
        }

        $dataContext = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        
        $prompt = [
            [
                'role' => 'system',
                'content' => "Anda adalah asisten toko PosPhone yang ramah dan profesional. 
Tugas: Jawab pertanyaan user berdasarkan data yang diberikan.
Format: Gunakan bahasa Indonesia yang natural dan friendly.
Data: $dataContext"
            ],
            [
                'role' => 'user',
                'content' => $userMessage
            ]
        ];

        return $this->callGroqAPI($prompt);
    }

    /**
     * Call Groq API
     */
    private function callGroqAPI(array $messages): string
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(30)->post($this->apiUrl, [
            'model' => $this->model,
            'messages' => $messages,
            'temperature' => 0.7,
            'max_tokens' => 1024,
        ]);

        if (!$response->successful()) {
            throw new \Exception('Groq API Error: ' . $response->body());
        }

        $result = $response->json();
        
        return $result['choices'][0]['message']['content'] ?? 'Maaf, tidak ada respons dari AI.';
    }

    /**
     * Extract toko ID dari message user
     */
    private function extractTokoId(?string $message, ?int $ownerId = null): ?int
    {
        if (!$message) {
            return null;
        }

        try {
            $prompt = [
                [
                    'role' => 'system',
                    'content' => "Ekstrak nama toko dari pertanyaan user. Jika ada nama toko yang disebut, jawab dengan HANYA nama toko tersebut (tanpa kata tambahan). Jika tidak ada nama toko, jawab 'NONE'.
Contoh:
- Input: 'Produk di toko miphone id apa saja?' → Output: 'miphone id'
- Input: 'Stok di toko ibong cell?' → Output: 'ibong cell'
- Input: 'Produk apa saja?' → Output: 'NONE'
Penting: Jawab HANYA nama toko, jangan tambah penjelasan."
                ],
                [
                    'role' => 'user',
                    'content' => $message
                ]
            ];

            $tokoName = strtolower(trim($this->callGroqAPI($prompt)));
            
            Log::info('Extracted toko name: ' . $tokoName);
            
            if ($tokoName === 'none' || empty($tokoName)) {
                return null;
            }

            // Cari toko di database dengan case-insensitive matching
            $toko = \App\Models\PosToko::query();
            
            if ($ownerId) {
                $toko->where('owner_id', $ownerId);
            }
            
            // Try exact match first (case-insensitive)
            $result = $toko->whereRaw('LOWER(nama) LIKE ?', ['%' . $tokoName . '%'])->first();
            
            if ($result) {
                Log::info('Found toko: ' . $result->nama . ' (ID: ' . $result->id . ')');
                return $result->id;
            }
            
            // Try slug matching
            $slugSearch = str_replace(' ', '-', $tokoName);
            $result = \App\Models\PosToko::query();
            if ($ownerId) {
                $result->where('owner_id', $ownerId);
            }
            $result = $result->whereRaw('LOWER(slug) LIKE ?', ['%' . $slugSearch . '%'])->first();
            
            if ($result) {
                Log::info('Found toko by slug: ' . $result->nama . ' (ID: ' . $result->id . ')');
                return $result->id;
            }
            
            Log::info('Toko not found for: ' . $tokoName);
            return null;
        } catch (\Exception $e) {
            Log::warning('Failed to extract toko ID: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Database Queries
     */
    private function getAllStores(?int $ownerId = null): array
    {
        $query = \App\Models\PosToko::select([
            'id',
            'owner_id',
            'nama',
            'slug',
            'alamat'
        ]);

        if ($ownerId) {
            $query->where('owner_id', $ownerId);
        }

        $stores = $query->orderBy('nama')->get();

        return $stores->map(function ($store) {
            return [
                'id' => $store->id,
                'nama' => $store->nama,
                'alamat' => $store->alamat,
                'slug' => $store->slug
            ];
        })->toArray();
    }

    private function getStoreProducts(?int $ownerId = null, ?int $tokoId = null): array
    {
        if ($tokoId) {
            // Get product IDs dari toko dengan stok > 0
            $productIds = \App\Models\PosProdukStok::where('pos_toko_id', $tokoId)
                ->where('stok', '>', 0)
                ->pluck('pos_produk_id')
                ->unique();
            
            if ($productIds->isEmpty()) {
                return [];
            }
            
            // Query produk berdasarkan ID
            $query = PosProduk::select([
                'pos_produk.id',
                'pos_produk.nama',
                'pos_produk.warna',
                'pos_produk.penyimpanan',
                'pos_produk.harga_jual'
            ])
                ->with('merk')
                ->whereIn('id', $productIds);
        } else {
            // Query semua produk
            $query = PosProduk::select([
                'pos_produk.id',
                'pos_produk.nama',
                'pos_produk.warna',
                'pos_produk.penyimpanan',
                'pos_produk.harga_jual'
            ])
                ->with('merk')
                ->limit(15);
        }

        if ($ownerId) {
            $query->where('pos_produk.owner_id', $ownerId);
        }

        $products = $query->get();

        return $products->map(function ($product) {
            return [
                'id' => $product->id,
                'nama' => $product->display_name,
                'merk' => $product->merk->nama ?? '-',
                'harga_jual' => 'Rp ' . number_format($product->harga_jual, 0, ',', '.'),
                'warna' => $product->warna,
                'storage' => $product->penyimpanan ? $product->penyimpanan . 'GB' : '-'
            ];
        })->toArray();
    }

    private function getStoreStock(?int $ownerId = null, ?int $tokoId = null): array
    {
        $query = \App\Models\PosProdukStok::with(['toko', 'produk.merk']);

        if ($tokoId) {
            $query->where('pos_toko_id', $tokoId);
        }

        if ($ownerId) {
            $query->where('owner_id', $ownerId);
        }

        $stocks = $query->orderBy('pos_toko_id')->get();

        // Group by toko
        $groupedByToko = [];
        foreach ($stocks as $stock) {
            $tokoNama = $stock->toko->nama ?? 'Unknown';
            
            if (!isset($groupedByToko[$tokoNama])) {
                $groupedByToko[$tokoNama] = [
                    'toko' => $tokoNama,
                    'total_stok' => 0,
                    'produk' => []
                ];
            }
            
            $groupedByToko[$tokoNama]['total_stok'] += $stock->stok;
            $groupedByToko[$tokoNama]['produk'][] = [
                'produk_nama' => $stock->produk->display_name ?? '-',
                'merk' => $stock->produk->merk->nama ?? '-',
                'stok' => $stock->stok,
                'warna' => $stock->produk->warna,
                'storage' => $stock->produk->penyimpanan ? $stock->produk->penyimpanan . 'GB' : '-'
            ];
        }

        return array_values($groupedByToko);
    }

    /**
     * Database Queries
     */
    private function getTopStores(?int $ownerId = null): array
    {
        $query = \App\Models\PosToko::select([
                'pos_toko.id',
                'pos_toko.owner_id',
                'pos_toko.nama',
                'pos_toko.slug',
                'pos_toko.alamat'
            ])
            ->selectRaw('COUNT(DISTINCT pos_transaksi.id) as total_transaksi')
            ->selectRaw('COALESCE(SUM(pos_transaksi.total_harga), 0) as total_penjualan')
            ->selectRaw('COALESCE(SUM(pos_transaksi_item.quantity), 0) as total_terjual')
            ->join('pos_transaksi', 'pos_toko.id', '=', 'pos_transaksi.pos_toko_id')
            ->join('pos_transaksi_item', 'pos_transaksi.id', '=', 'pos_transaksi_item.pos_transaksi_id')
            ->where('pos_transaksi.is_transaksi_masuk', 1);

        if ($ownerId) {
            $query->where('pos_toko.owner_id', $ownerId);
        }

        $stores = $query->groupBy([
                'pos_toko.id',
                'pos_toko.owner_id',
                'pos_toko.nama',
                'pos_toko.slug',
                'pos_toko.alamat'
            ])
            ->orderByDesc('total_penjualan')
            ->limit(5)
            ->get();

        return $stores->map(function ($store) {
            return [
                'nama' => $store->nama,
                'alamat' => $store->alamat,
                'total_transaksi' => $store->total_transaksi ?? 0,
                'total_terjual' => $store->total_terjual ?? 0,
                'total_penjualan' => 'Rp ' . number_format($store->total_penjualan, 0, ',', '.'),
                'slug' => $store->slug
            ];
        })->toArray();
    }

    private function getTopProducts(?int $ownerId = null): array
    {
        $query = PosProduk::select([
                'pos_produk.id',
                'pos_produk.owner_id',
                'pos_produk.pos_produk_merk_id',
                'pos_produk.nama',
                'pos_produk.warna',
                'pos_produk.penyimpanan',
                'pos_produk.harga_jual'
            ])
            ->selectRaw('COALESCE(SUM(pos_transaksi_item.quantity), 0) as total_terjual')
            ->join('pos_transaksi_item', 'pos_produk.id', '=', 'pos_transaksi_item.pos_produk_id')
            ->join('pos_transaksi', 'pos_transaksi_item.pos_transaksi_id', '=', 'pos_transaksi.id')
            ->where('pos_transaksi.is_transaksi_masuk', 1);

        if ($ownerId) {
            $query->where('pos_produk.owner_id', $ownerId);
        }

        $products = $query->groupBy([
                'pos_produk.id',
                'pos_produk.owner_id',
                'pos_produk.pos_produk_merk_id',
                'pos_produk.nama',
                'pos_produk.warna',
                'pos_produk.penyimpanan',
                'pos_produk.harga_jual'
            ])
            ->orderByDesc('total_terjual')
            ->limit(5)
            ->get();

        // Eager load merk relationships after query
        $products->load('merk');

        return $products->map(function ($product) {
            return [
                'nama' => $product->display_name,
                'merk' => $product->merk->nama ?? '-',
                'harga_jual' => number_format($product->harga_jual, 0, ',', '.'),
                'total_terjual' => $product->total_terjual ?? 0,
                'warna' => $product->warna,
                'storage' => $product->penyimpanan ? $product->penyimpanan . 'GB' : '-'
            ];
        })->toArray();
    }

    private function getSalesToday(?int $ownerId = null): array
    {
        $query = \App\Models\PosTransaksi::where('is_transaksi_masuk', 1)
            ->whereDate('created_at', now()->toDateString());

        if ($ownerId) {
            $query->where('owner_id', $ownerId);
        }

        $transactions = $query->get();
        $totalTransaksi = $transactions->count();
        $totalPendapatan = $transactions->sum('total_harga');

        // Hitung total produk terjual dari items
        $totalProdukTerjual = \App\Models\PosTransaksiItem::whereIn(
            'pos_transaksi_id', 
            $transactions->pluck('id')
        )->sum('quantity');

        return [
            'total_transaksi' => $totalTransaksi,
            'total_pendapatan' => 'Rp ' . number_format($totalPendapatan, 0, ',', '.'),
            'produk_terjual' => $totalProdukTerjual,
            'tanggal' => now()->format('d F Y')
        ];
    }

    private function getStockSummary(?int $ownerId = null): array
    {
        $query = ProdukStok::with('produk.merk')
            ->where('jumlah', '>', 0);

        if ($ownerId) {
            $query->whereHas('produk', function ($q) use ($ownerId) {
                $q->where('owner_id', $ownerId);
            });
        }

        $stocks = $query->get();

        return [
            'total_produk' => $stocks->count(),
            'total_stok' => $stocks->sum('jumlah'),
            'produk_low_stock' => $stocks->where('jumlah', '<', 5)->count(),
            'details' => $stocks->take(10)->map(function ($stok) {
                return [
                    'produk' => $stok->produk->display_name ?? '-',
                    'jumlah' => $stok->jumlah,
                    'lokasi' => $stok->lokasi ?? 'Gudang'
                ];
            })->toArray()
        ];
    }

    private function getAllProducts(?int $ownerId = null): array
    {
        $query = PosProduk::with(['merk'])->limit(10);

        if ($ownerId) {
            $query->where('owner_id', $ownerId);
        }

        $products = $query->get();

        return $products->map(function ($product) {
            return [
                'nama' => $product->display_name,
                'merk' => $product->merk->nama ?? '-',
                'harga_jual' => number_format($product->harga_jual, 0, ',', '.'),
                'warna' => $product->warna,
                'storage' => $product->penyimpanan ? $product->penyimpanan . 'GB' : '-'
            ];
        })->toArray();
    }

    private function getProductPrices(?int $ownerId = null): array
    {
        $query = PosProduk::with(['merk'])->limit(10);

        if ($ownerId) {
            $query->where('owner_id', $ownerId);
        }

        $products = $query->get();

        return $products->map(function ($product) {
            return [
                'nama' => $product->display_name,
                'harga_beli' => number_format($product->harga_beli, 0, ',', '.'),
                'harga_jual' => number_format($product->harga_jual, 0, ',', '.'),
                'margin' => number_format($product->harga_jual - $product->harga_beli, 0, ',', '.')
            ];
        })->toArray();
    }
}
