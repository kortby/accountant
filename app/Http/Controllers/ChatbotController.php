<?php

namespace App\Http\Controllers;

use App\Services\AiChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ChatbotController extends Controller
{
    protected int $maxQuestions = 5;

    protected int $cacheTtlSeconds = 86400; // 24 hours

    /**
     * Handle a chatbot message.
     */
    public function chat(Request $request, AiChatService $chatService): JsonResponse
    {
        $request->validate([
            'message' => ['required', 'string', 'max:500'],
            'history' => ['nullable', 'array', 'max:20'],
            'history.*.role' => ['required_with:history', 'string', 'in:user,assistant'],
            'history.*.content' => ['required_with:history', 'string', 'max:2000'],
        ]);

        $ip = $request->ip();
        $cacheKey = "chatbot_ip:{$ip}";
        $questionsUsed = (int) Cache::get($cacheKey, 0);

        if ($questionsUsed >= $this->maxQuestions) {
            return response()->json([
                'reply' => null,
                'questions_remaining' => 0,
                'limit_reached' => true,
            ]);
        }

        $history = $request->input('history', []);
        $reply = $chatService->chat($request->input('message'), $history);

        Cache::put($cacheKey, $questionsUsed + 1, $this->cacheTtlSeconds);

        $remaining = $this->maxQuestions - ($questionsUsed + 1);

        return response()->json([
            'reply' => $reply,
            'questions_remaining' => $remaining,
            'limit_reached' => $remaining <= 0,
        ]);
    }
}
