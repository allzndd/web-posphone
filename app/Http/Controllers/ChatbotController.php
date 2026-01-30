<?php

namespace App\Http\Controllers;

use App\Services\AIChatbotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChatbotController extends Controller
{
    private AIChatbotService $chatbotService;

    public function __construct(AIChatbotService $chatbotService)
    {
        $this->chatbotService = $chatbotService;
    }

    /**
     * Handle incoming chat message
     * 
     * POST /api/chatbot/message
     * Body: { 
     *   "message": "produk terlaris bulan ini apa?",
     *   "owner_id": 1
     * }
     */
    public function chat(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:1000',
            'owner_id' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $message = $request->input('message');
        
        // Priority: body owner_id > authenticated user > null
        $ownerId = $request->input('owner_id');
        if (!$ownerId && auth()->check()) {
            $ownerId = auth()->id();
        }

        // Call AI chatbot service
        $result = $this->chatbotService->chat($message, $ownerId);

        return response()->json($result);
    }

    /**
     * Get chatbot info/status
     * 
     * GET /api/chatbot/info
     */
    public function info()
    {
        return response()->json([
            'success' => true,
            'service' => 'PosPhone AI Chatbot',
            'model' => config('services.groq.model'),
            'capabilities' => [
                'LIST_STORES' => 'Menampilkan daftar semua toko',
                'STORE_PRODUCTS' => 'Menampilkan produk di toko',
                'STORE_STOCK' => 'Menampilkan stok produk per toko',
                'TOP_PRODUCT' => 'Menanyakan produk terlaris',
                'TOP_STORE' => 'Menanyakan toko dengan penjualan terbanyak',
                'SALES_TODAY' => 'Menanyakan penjualan hari ini',
                'STOCK_PRODUCT' => 'Menanyakan stok produk',
                'PRODUCT_SEARCH' => 'Mencari produk',
                'PRODUCT_PRICE' => 'Menanyakan harga produk'
            ],
            'example_queries' => [
                'Toko apa saja?',
                'Produk di toko ini apa saja?',
                'Stok produk di toko ini berapa?',
                'Produk terlaris bulan ini apa?',
                'Toko mana yang paling banyak penjualan?',
                'Berapa total penjualan hari ini?',
                'Cek stok produk yang tersedia',
                'Cari iPhone 13',
                'Berapa harga Samsung Galaxy?'
            ]
        ]);
    }
}
