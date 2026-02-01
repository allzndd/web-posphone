<?php

namespace App\Http\Controllers;

use App\Services\AnalyticsChatService;
use Illuminate\Http\Request;

class ChatAnalysisController extends Controller
{
    protected AnalyticsChatService $service;

    public function __construct(AnalyticsChatService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return view('pages.chat.index');
    }

    public function ask(Request $request)
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:500']
        ]);

        try {
            $result = $this->service->answer($data['message']);

            return response()->json([
                'ok' => true,
                'answer' => $result['answer'] ?? '',
                'data' => $result['data'] ?? null,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'ok' => false,
                'error' => 'Terjadi kesalahan saat memproses pertanyaan.',
            ], 500);
        }
    }
}
