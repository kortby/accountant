<?php

use Gemini\Laravel\Facades\Gemini;
use Gemini\Responses\GenerativeModel\GenerateContentResponse;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    Cache::flush();
});

test('chatbot returns a response for a valid message', function () {
    Gemini::fake([
        GenerateContentResponse::fake([
            'candidates' => [
                [
                    'content' => [
                        'parts' => [
                            ['text' => 'We offer individual tax preparation, business tax services, and more.'],
                        ],
                    ],
                ],
            ],
        ]),
    ]);

    $response = $this->postJson(route('chatbot.chat'), [
        'message' => 'What services do you offer?',
    ]);

    $response->assertSuccessful()
        ->assertJsonStructure(['reply', 'questions_remaining', 'limit_reached'])
        ->assertJson([
            'questions_remaining' => 4,
            'limit_reached' => false,
        ]);

    expect($response->json('reply'))->not->toBeNull();
});

test('chatbot tracks question count by ip address', function () {
    Gemini::fake([
        GenerateContentResponse::fake([
            'candidates' => [
                [
                    'content' => [
                        'parts' => [
                            ['text' => 'Response 1'],
                        ],
                    ],
                ],
            ],
        ]),
        GenerateContentResponse::fake([
            'candidates' => [
                [
                    'content' => [
                        'parts' => [
                            ['text' => 'Response 2'],
                        ],
                    ],
                ],
            ],
        ]),
    ]);

    $this->postJson(route('chatbot.chat'), [
        'message' => 'Question 1',
    ])->assertJson(['questions_remaining' => 4]);

    $this->postJson(route('chatbot.chat'), [
        'message' => 'Question 2',
    ])->assertJson(['questions_remaining' => 3]);
});

test('chatbot enforces 5 question limit per ip', function () {
    // Pre-fill the cache with 5 questions used
    Cache::put('chatbot_ip:127.0.0.1', 5, 86400);

    $response = $this->postJson(route('chatbot.chat'), [
        'message' => 'One more question?',
    ]);

    $response->assertSuccessful()
        ->assertJson([
            'reply' => null,
            'questions_remaining' => 0,
            'limit_reached' => true,
        ]);
});

test('chatbot returns limit reached on the 5th question', function () {
    Cache::put('chatbot_ip:127.0.0.1', 4, 86400);

    Gemini::fake([
        GenerateContentResponse::fake([
            'candidates' => [
                [
                    'content' => [
                        'parts' => [
                            ['text' => 'This is your last answer.'],
                        ],
                    ],
                ],
            ],
        ]),
    ]);

    $response = $this->postJson(route('chatbot.chat'), [
        'message' => 'Last question',
    ]);

    $response->assertSuccessful()
        ->assertJson([
            'questions_remaining' => 0,
            'limit_reached' => true,
        ]);

    expect($response->json('reply'))->not->toBeNull();
});

test('chatbot validates message is required', function () {
    $response = $this->postJson(route('chatbot.chat'), [
        'message' => '',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors('message');
});

test('chatbot validates message max length', function () {
    $response = $this->postJson(route('chatbot.chat'), [
        'message' => str_repeat('a', 501),
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors('message');
});

test('chatbot accepts conversation history', function () {
    Gemini::fake([
        GenerateContentResponse::fake([
            'candidates' => [
                [
                    'content' => [
                        'parts' => [
                            ['text' => 'Based on our conversation, I can help further.'],
                        ],
                    ],
                ],
            ],
        ]),
    ]);

    $response = $this->postJson(route('chatbot.chat'), [
        'message' => 'Can you tell me more?',
        'history' => [
            ['role' => 'user', 'content' => 'What services do you offer?'],
            ['role' => 'assistant', 'content' => 'We offer tax preparation and bookkeeping.'],
        ],
    ]);

    $response->assertSuccessful();
    expect($response->json('reply'))->not->toBeNull();
});

test('chatbot validates history role values', function () {
    $response = $this->postJson(route('chatbot.chat'), [
        'message' => 'Hello',
        'history' => [
            ['role' => 'invalid', 'content' => 'test'],
        ],
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors('history.0.role');
});

test('chatbot does not call gemini when limit already reached', function () {
    Cache::put('chatbot_ip:127.0.0.1', 5, 86400);

    // Do NOT fake Gemini - if it's called, it would fail
    $response = $this->postJson(route('chatbot.chat'), [
        'message' => 'Try again',
    ]);

    $response->assertSuccessful()
        ->assertJson([
            'reply' => null,
            'limit_reached' => true,
        ]);
});
