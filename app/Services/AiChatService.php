<?php

namespace App\Services;

use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\Log;

class AiChatService
{
    protected string $model = 'gemini-2.0-flash';

    protected string $knowledgeBasePath;

    public function __construct()
    {
        $this->knowledgeBasePath = storage_path('app/ai-knowledge-base.md');
    }

    /**
     * Send a message to the AI chatbot and get a response.
     *
     * @param  array<int, array{role: string, content: string}>  $conversationHistory
     */
    public function chat(string $message, array $conversationHistory = []): string
    {
        $knowledgeBase = $this->loadKnowledgeBase();
        $systemPrompt = $this->buildSystemPrompt($knowledgeBase);

        $contents = $this->buildContents($systemPrompt, $conversationHistory, $message);

        try {
            $result = Gemini::generativeModel(model: $this->model)
                ->generateContent($contents);

            return trim($result->text());
        } catch (\Exception $e) {
            Log::error('AiChatService: Failed to generate response', [
                'error' => $e->getMessage(),
            ]);

            return 'I apologize, but I\'m having trouble responding right now. Please contact us directly at (310) 848-8598 or support@taxesaccountant.co for immediate assistance.';
        }
    }

    /**
     * Load the knowledge base content.
     */
    protected function loadKnowledgeBase(): string
    {
        if (! file_exists($this->knowledgeBasePath)) {
            Log::warning('AiChatService: Knowledge base file not found', [
                'path' => $this->knowledgeBasePath,
            ]);

            return '';
        }

        return file_get_contents($this->knowledgeBasePath);
    }

    /**
     * Build the system prompt with the knowledge base.
     */
    protected function buildSystemPrompt(string $knowledgeBase): string
    {
        return <<<PROMPT
You are the AI assistant for TaxesAccountant, a professional tax preparation and accounting firm. Your role is to help website visitors learn about services, answer common questions, and guide them toward booking a consultation.

IMPORTANT RULES:
- Be warm, professional, concise, and helpful.
- Answer only based on the knowledge base provided below.
- Do NOT provide specific tax advice, legal advice, or financial recommendations for individual situations.
- If asked about personal tax calculations or legal matters, recommend they book a consultation or call (310) 848-8598.
- Never fabricate pricing amounts, tax rules, or legal requirements.
- Keep responses brief — 2-4 sentences for simple questions, up to a short paragraph for complex topics.
- When relevant, suggest booking a consultation at /book or calling (310) 848-8598.
- If you don't know the answer, say so honestly and recommend contacting the firm.
- Use simple, non-technical language when possible.
- Do not use markdown formatting in responses. Use plain text only.

KNOWLEDGE BASE:
{$knowledgeBase}
PROMPT;
    }

    /**
     * Build the content array for the Gemini API call.
     *
     * @param  array<int, array{role: string, content: string}>  $conversationHistory
     * @return array<int, string>
     */
    protected function buildContents(string $systemPrompt, array $conversationHistory, string $currentMessage): array
    {
        $parts = [$systemPrompt."\n\n"];

        foreach ($conversationHistory as $entry) {
            $role = $entry['role'] === 'user' ? 'User' : 'Assistant';
            $parts[] = "{$role}: {$entry['content']}\n";
        }

        $parts[] = "User: {$currentMessage}\nAssistant:";

        return [implode('', $parts)];
    }
}
