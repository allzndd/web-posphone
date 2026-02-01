<?php

namespace App\Http\Controllers;

use App\Services\AIChatbotService;
use Illuminate\Http\Request;

class ChatAnalysisController extends Controller
{
    protected AIChatbotService $chatbotService;

    public function __construct(AIChatbotService $chatbotService)
    {
        $this->chatbotService = $chatbotService;
    }

    public function index()
    {
        return view('pages.chat.index');
    }

    public function ask(Request $request)
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:1000']
        ]);

        try {
            // Get owner_id from authenticated user (same pattern as ProdukController)
            $user = auth()->user();
            $ownerId = $user->owner ? $user->owner->id : null;
            
            // Call AI Chatbot Service
            $result = $this->chatbotService->chat($data['message'], $ownerId);

            if ($result['success']) {
                return response()->json([
                    'ok' => true,
                    'answer' => $result['message'],
                    'intent' => $result['intent'],
                    'data' => $result['data'],
                ]);
            } else {
                return response()->json([
                    'ok' => false,
                    'error' => $result['error'] ?? 'Terjadi kesalahan saat memproses pertanyaan.',
                ], 500);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'ok' => false,
                'error' => 'Terjadi kesalahan saat memproses pertanyaan.',
                'debug' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
