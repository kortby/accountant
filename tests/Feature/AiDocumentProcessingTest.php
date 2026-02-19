<?php

use App\Jobs\ProcessTaxDocuments;
use App\Models\DocumentExtraction;
use App\Models\TaxReturn;
use App\Models\User;
use App\Services\GeminiDocumentProcessor;
use Gemini\Laravel\Facades\Gemini;
use Gemini\Responses\GenerativeModel\GenerateContentResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

test('ai processing job is dispatched after tax return submission with documents', function () {
    Queue::fake();
    Storage::fake('public');

    $user = User::factory()->create(['role' => 'client']);

    $this->actingAs($user);

    $response = $this->post(route('tax-information.store'), [
        'tax_year' => 2025,
        'ssn' => '123-45-6789',
        'date_of_birth' => '1990-01-01',
        'occupation' => 'Developer',
        'marital_status' => 'single',
        'address' => '456 Oak Ave',
        'city' => 'Austin',
        'state' => 'TX',
        'zip_code' => '73301',
        'income_types' => ['w2'],
        'dependents' => [],
        'documents' => [
            UploadedFile::fake()->create('w2.pdf', 100, 'application/pdf'),
        ],
    ]);

    $response->assertRedirect(route('dashboard'));

    $taxReturn = TaxReturn::where('user_id', $user->id)->first();
    expect($taxReturn)->not->toBeNull()
        ->and($taxReturn->ai_processing_status)->toBe('pending');

    Queue::assertPushed(ProcessTaxDocuments::class, function ($job) use ($taxReturn) {
        return $job->taxReturnId === $taxReturn->id;
    });
});

test('ai processing job is not dispatched when no documents are uploaded', function () {
    Queue::fake();

    $user = User::factory()->create(['role' => 'client']);

    $this->actingAs($user);

    $this->post(route('tax-information.store'), [
        'tax_year' => 2025,
        'ssn' => '123-45-6789',
        'date_of_birth' => '1990-01-01',
        'occupation' => 'Developer',
        'marital_status' => 'single',
        'address' => '456 Oak Ave',
        'city' => 'Austin',
        'state' => 'TX',
        'zip_code' => '73301',
        'income_types' => ['w2'],
        'dependents' => [],
        'documents' => [],
    ]);

    Queue::assertNotPushed(ProcessTaxDocuments::class);
});

test('process tax documents job creates income sources from ai extraction', function () {
    Storage::fake('public');

    $geminiResponse = json_encode([
        'document_type' => 'w2',
        'confidence' => 0.95,
        'employer_name' => 'Acme Corp',
        'employer_ein' => '12-3456789',
        'income_items' => [
            [
                'type' => 'w2',
                'source_name' => 'Employment Income',
                'amount' => 75000.00,
                'federal_tax_withheld' => 12000.00,
                'state_tax_withheld' => 3500.00,
                'state' => 'TX',
            ],
        ],
        'deductions' => [],
        'taxpayer_info' => ['name' => 'John Doe'],
    ]);

    Gemini::fake([
        GenerateContentResponse::fake([
            'candidates' => [
                [
                    'content' => [
                        'parts' => [
                            ['text' => $geminiResponse],
                        ],
                    ],
                ],
            ],
        ]),
    ]);

    $user = User::factory()->create(['role' => 'client']);
    $taxReturn = TaxReturn::create([
        'user_id' => $user->id,
        'tax_year' => 2025,
        'status' => TaxReturn::STATUS_SUBMITTED,
        'submitted_at' => now(),
        'total_income' => 0,
        'taxable_income' => 0,
        'tax_liability' => 0,
        'total_deductions' => 0,
        'total_credits' => 0,
        'amount_due' => 0,
        'refund_amount' => 0,
        'ai_processing_status' => 'pending',
    ]);

    // Create an initial income source (like store() does)
    $taxReturn->incomeSources()->create([
        'type' => 'w2',
        'source_name' => 'Employment Income',
        'amount' => 0,
    ]);

    // Add a fake document
    $taxReturn->addMedia(UploadedFile::fake()->create('w2.pdf', 100, 'application/pdf'))
        ->toMediaCollection('documents');

    // Run the job
    $job = new ProcessTaxDocuments($taxReturn->id);
    $job->handle(app(GeminiDocumentProcessor::class));

    $taxReturn->refresh();

    expect($taxReturn->ai_processing_status)->toBe('completed')
        ->and($taxReturn->ai_processed_at)->not->toBeNull()
        ->and($taxReturn->total_income)->toBe('75000.00')
        ->and($taxReturn->federal_tax_withheld)->toBe('12000.00')
        ->and((float) $taxReturn->taxable_income)->toBe(75000.00)
        ->and((float) $taxReturn->tax_liability)->toBeGreaterThan(0)
        ->and((float) $taxReturn->refund_amount)->toBeGreaterThan(0);

    $incomeSource = $taxReturn->incomeSources()->where('type', 'w2')->first();
    expect($incomeSource)->not->toBeNull()
        ->and($incomeSource->amount)->toBe('75000.00')
        ->and($incomeSource->employer_name)->toBe('Acme Corp')
        ->and($incomeSource->payer_ein)->toBe('12-3456789')
        ->and($incomeSource->ai_extracted)->toBeTrue()
        ->and((float) $incomeSource->ai_confidence)->toBe(0.95);
});

test('process tax documents job creates deductions from ai extraction', function () {
    Storage::fake('public');

    $geminiResponse = json_encode([
        'document_type' => '1098',
        'confidence' => 0.90,
        'employer_name' => 'First National Bank',
        'employer_ein' => '98-7654321',
        'income_items' => [],
        'deductions' => [
            [
                'category' => 'mortgage_interest',
                'amount' => 15000.00,
                'description' => 'Mortgage interest paid',
            ],
        ],
        'taxpayer_info' => [],
    ]);

    Gemini::fake([
        GenerateContentResponse::fake([
            'candidates' => [
                [
                    'content' => [
                        'parts' => [
                            ['text' => $geminiResponse],
                        ],
                    ],
                ],
            ],
        ]),
    ]);

    $user = User::factory()->create(['role' => 'client']);
    $taxReturn = TaxReturn::create([
        'user_id' => $user->id,
        'tax_year' => 2025,
        'status' => TaxReturn::STATUS_SUBMITTED,
        'submitted_at' => now(),
        'total_income' => 0,
        'taxable_income' => 0,
        'tax_liability' => 0,
        'total_deductions' => 0,
        'total_credits' => 0,
        'amount_due' => 0,
        'refund_amount' => 0,
        'ai_processing_status' => 'pending',
    ]);

    $taxReturn->addMedia(UploadedFile::fake()->create('1098.pdf', 100, 'application/pdf'))
        ->toMediaCollection('documents');

    $job = new ProcessTaxDocuments($taxReturn->id);
    $job->handle(app(GeminiDocumentProcessor::class));

    $taxReturn->refresh();

    expect($taxReturn->ai_processing_status)->toBe('completed')
        ->and($taxReturn->total_deductions)->toBe('15000.00');

    $deduction = $taxReturn->deductions()->first();
    expect($deduction)->not->toBeNull()
        ->and($deduction->category)->toBe('mortgage_interest')
        ->and($deduction->amount)->toBe('15000.00');
});

test('document extraction records are created for each processed file', function () {
    Storage::fake('public');

    $geminiResponse = json_encode([
        'document_type' => 'w2',
        'confidence' => 0.92,
        'employer_name' => 'Test Corp',
        'employer_ein' => '11-1111111',
        'income_items' => [
            ['type' => 'w2', 'source_name' => 'Wages', 'amount' => 50000, 'federal_tax_withheld' => 8000, 'state_tax_withheld' => 2000, 'state' => 'CA'],
        ],
        'deductions' => [],
        'taxpayer_info' => [],
    ]);

    Gemini::fake([
        GenerateContentResponse::fake([
            'candidates' => [['content' => ['parts' => [['text' => $geminiResponse]]]]],
        ]),
    ]);

    $user = User::factory()->create(['role' => 'client']);
    $taxReturn = TaxReturn::create([
        'user_id' => $user->id,
        'tax_year' => 2025,
        'status' => TaxReturn::STATUS_SUBMITTED,
        'submitted_at' => now(),
        'total_income' => 0,
        'taxable_income' => 0,
        'tax_liability' => 0,
        'total_deductions' => 0,
        'total_credits' => 0,
        'amount_due' => 0,
        'refund_amount' => 0,
        'ai_processing_status' => 'pending',
    ]);

    $taxReturn->addMedia(UploadedFile::fake()->create('w2.pdf', 100, 'application/pdf'))
        ->toMediaCollection('documents');

    $job = new ProcessTaxDocuments($taxReturn->id);
    $job->handle(app(GeminiDocumentProcessor::class));

    $extraction = DocumentExtraction::where('tax_return_id', $taxReturn->id)->first();

    expect($extraction)->not->toBeNull()
        ->and($extraction->status)->toBe('completed')
        ->and($extraction->document_type)->toBe('w2')
        ->and($extraction->confidence_score)->toBe('0.92')
        ->and($extraction->extracted_data)->toBeArray()
        ->and($extraction->processed_at)->not->toBeNull();
});

test('ai status endpoint returns current processing status', function () {
    $user = User::factory()->create(['role' => 'client']);
    $taxReturn = TaxReturn::create([
        'user_id' => $user->id,
        'tax_year' => 2025,
        'status' => TaxReturn::STATUS_SUBMITTED,
        'submitted_at' => now(),
        'total_income' => 0,
        'taxable_income' => 0,
        'tax_liability' => 0,
        'total_deductions' => 0,
        'total_credits' => 0,
        'amount_due' => 0,
        'refund_amount' => 0,
        'ai_processing_status' => 'processing',
    ]);

    $this->actingAs($user);

    $response = $this->getJson(route('tax-returns.ai-status', $taxReturn));

    $response->assertOk()
        ->assertJson([
            'ai_processing_status' => 'processing',
        ]);
});

test('ai status endpoint is forbidden for other clients', function () {
    $owner = User::factory()->create(['role' => 'client']);
    $other = User::factory()->create(['role' => 'client']);

    $taxReturn = TaxReturn::create([
        'user_id' => $owner->id,
        'tax_year' => 2025,
        'status' => TaxReturn::STATUS_SUBMITTED,
        'submitted_at' => now(),
        'total_income' => 0,
        'taxable_income' => 0,
        'tax_liability' => 0,
        'total_deductions' => 0,
        'total_credits' => 0,
        'amount_due' => 0,
        'refund_amount' => 0,
    ]);

    $this->actingAs($other);

    $response = $this->getJson(route('tax-returns.ai-status', $taxReturn));

    $response->assertForbidden();
});

test('gemini document processor parses valid json response', function () {
    $processor = new GeminiDocumentProcessor;

    $parseMethod = new ReflectionMethod($processor, 'parseResponse');

    $result = $parseMethod->invoke($processor, json_encode([
        'document_type' => 'w2',
        'confidence' => 0.88,
        'employer_name' => 'Test Inc',
        'employer_ein' => '22-3344556',
        'income_items' => [
            ['type' => 'w2', 'source_name' => 'Wages', 'amount' => 60000, 'federal_tax_withheld' => 9000, 'state_tax_withheld' => 2500, 'state' => 'NY'],
        ],
        'deductions' => [],
        'taxpayer_info' => ['name' => 'Jane Smith'],
    ]));

    expect($result['document_type'])->toBe('w2')
        ->and($result['confidence'])->toBe(0.88)
        ->and($result['employer_name'])->toBe('Test Inc')
        ->and($result['income_items'])->toHaveCount(1)
        ->and($result['income_items'][0]['amount'])->toBe(60000.0);
});

test('gemini document processor handles markdown-wrapped json', function () {
    $processor = new GeminiDocumentProcessor;

    $parseMethod = new ReflectionMethod($processor, 'parseResponse');

    $wrappedJson = "```json\n".json_encode([
        'document_type' => 'w2',
        'confidence' => 0.9,
        'employer_name' => null,
        'employer_ein' => null,
        'income_items' => [],
        'deductions' => [],
        'taxpayer_info' => [],
    ])."\n```";

    $result = $parseMethod->invoke($processor, $wrappedJson);

    expect($result['document_type'])->toBe('w2')
        ->and($result['confidence'])->toBe(0.9);
});

test('gemini document processor returns empty result for invalid json', function () {
    $processor = new GeminiDocumentProcessor;

    $parseMethod = new ReflectionMethod($processor, 'parseResponse');

    $result = $parseMethod->invoke($processor, 'this is not json');

    expect($result['document_type'])->toBe('other')
        ->and($result['confidence'])->toBe(0.0)
        ->and($result['income_items'])->toBeEmpty()
        ->and($result['deductions'])->toBeEmpty();
});

test('accountant can cancel ai processing', function () {
    $accountant = User::factory()->create(['role' => 'accountant']);
    $client = User::factory()->create(['role' => 'client']);

    $taxReturn = TaxReturn::create([
        'user_id' => $client->id,
        'tax_year' => 2025,
        'status' => TaxReturn::STATUS_SUBMITTED,
        'submitted_at' => now(),
        'total_income' => 0,
        'taxable_income' => 0,
        'tax_liability' => 0,
        'total_deductions' => 0,
        'total_credits' => 0,
        'amount_due' => 0,
        'refund_amount' => 0,
        'ai_processing_status' => 'processing',
    ]);

    // Create a pending extraction
    DocumentExtraction::create([
        'tax_return_id' => $taxReturn->id,
        'media_id' => 1,
        'status' => 'processing',
    ]);

    $this->actingAs($accountant);

    $response = $this->postJson(route('tax-returns.cancel-ai', $taxReturn));

    $response->assertOk()
        ->assertJson(['ai_processing_status' => 'cancelled']);

    $taxReturn->refresh();
    expect($taxReturn->ai_processing_status)->toBe('cancelled');

    $extraction = DocumentExtraction::where('tax_return_id', $taxReturn->id)->first();
    expect($extraction->status)->toBe('failed')
        ->and($extraction->error_message)->toBe('Cancelled by accountant');
});

test('client cannot cancel ai processing', function () {
    $client = User::factory()->create(['role' => 'client']);

    $taxReturn = TaxReturn::create([
        'user_id' => $client->id,
        'tax_year' => 2025,
        'status' => TaxReturn::STATUS_SUBMITTED,
        'submitted_at' => now(),
        'total_income' => 0,
        'taxable_income' => 0,
        'tax_liability' => 0,
        'total_deductions' => 0,
        'total_credits' => 0,
        'amount_due' => 0,
        'refund_amount' => 0,
        'ai_processing_status' => 'processing',
    ]);

    $this->actingAs($client);

    $response = $this->postJson(route('tax-returns.cancel-ai', $taxReturn));

    $response->assertForbidden();

    $taxReturn->refresh();
    expect($taxReturn->ai_processing_status)->toBe('processing');
});

test('job skips unsupported file types and creates failed extraction', function () {
    Storage::fake('public');

    $user = User::factory()->create(['role' => 'client']);
    $taxReturn = TaxReturn::create([
        'user_id' => $user->id,
        'tax_year' => 2025,
        'status' => TaxReturn::STATUS_SUBMITTED,
        'submitted_at' => now(),
        'total_income' => 0,
        'taxable_income' => 0,
        'tax_liability' => 0,
        'total_deductions' => 0,
        'total_credits' => 0,
        'amount_due' => 0,
        'refund_amount' => 0,
        'ai_processing_status' => 'pending',
    ]);

    // Add a .docx file (unsupported MIME type for AI)
    $taxReturn->addMedia(UploadedFile::fake()->create('contract.docx', 100, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'))
        ->toMediaCollection('documents');

    $job = new ProcessTaxDocuments($taxReturn->id);
    $job->handle(app(GeminiDocumentProcessor::class));

    $taxReturn->refresh();
    expect($taxReturn->ai_processing_status)->toBe('failed');

    $extraction = DocumentExtraction::where('tax_return_id', $taxReturn->id)->first();
    expect($extraction)->not->toBeNull()
        ->and($extraction->status)->toBe('failed')
        ->and($extraction->error_message)->toContain('Unsupported file type');
});

test('job skips files exceeding size limit', function () {
    Storage::fake('public');

    $user = User::factory()->create(['role' => 'client']);
    $taxReturn = TaxReturn::create([
        'user_id' => $user->id,
        'tax_year' => 2025,
        'status' => TaxReturn::STATUS_SUBMITTED,
        'submitted_at' => now(),
        'total_income' => 0,
        'taxable_income' => 0,
        'tax_liability' => 0,
        'total_deductions' => 0,
        'total_credits' => 0,
        'amount_due' => 0,
        'refund_amount' => 0,
        'ai_processing_status' => 'pending',
    ]);

    // Add a file larger than 5MB (use .pdf extension so it passes MIME check)
    $taxReturn->addMedia(UploadedFile::fake()->create('huge-scan.pdf', 6000, 'application/pdf'))
        ->toMediaCollection('documents');

    // Manually set the file size above the limit since fake files report actual disk size
    $media = $taxReturn->getMedia('documents')->first();
    $media->size = 6 * 1024 * 1024; // 6MB
    $media->save();

    $job = new ProcessTaxDocuments($taxReturn->id);
    $job->handle(app(GeminiDocumentProcessor::class));

    $taxReturn->refresh();
    expect($taxReturn->ai_processing_status)->toBe('failed');

    $extraction = DocumentExtraction::where('tax_return_id', $taxReturn->id)->first();
    expect($extraction)->not->toBeNull()
        ->and($extraction->status)->toBe('failed')
        ->and($extraction->error_message)->toContain('File too large');
});

test('job stops processing when cancelled mid-run', function () {
    Storage::fake('public');

    $geminiResponse = json_encode([
        'document_type' => 'w2',
        'confidence' => 0.95,
        'employer_name' => 'Test Corp',
        'employer_ein' => '11-1111111',
        'income_items' => [
            ['type' => 'w2', 'source_name' => 'Wages', 'amount' => 50000, 'federal_tax_withheld' => 8000, 'state_tax_withheld' => 2000, 'state' => 'TX'],
        ],
        'deductions' => [],
        'taxpayer_info' => [],
    ]);

    Gemini::fake([
        GenerateContentResponse::fake([
            'candidates' => [['content' => ['parts' => [['text' => $geminiResponse]]]]],
        ]),
    ]);

    $user = User::factory()->create(['role' => 'client']);
    $taxReturn = TaxReturn::create([
        'user_id' => $user->id,
        'tax_year' => 2025,
        'status' => TaxReturn::STATUS_SUBMITTED,
        'submitted_at' => now(),
        'total_income' => 0,
        'taxable_income' => 0,
        'tax_liability' => 0,
        'total_deductions' => 0,
        'total_credits' => 0,
        'amount_due' => 0,
        'refund_amount' => 0,
        'ai_processing_status' => 'cancelled',
    ]);

    $taxReturn->addMedia(UploadedFile::fake()->create('w2.pdf', 100, 'application/pdf'))
        ->toMediaCollection('documents');

    $job = new ProcessTaxDocuments($taxReturn->id);
    $job->handle(app(GeminiDocumentProcessor::class));

    // No extractions should be created since it was already cancelled
    expect(DocumentExtraction::where('tax_return_id', $taxReturn->id)->count())->toBe(0);

    // Status should remain cancelled
    $taxReturn->refresh();
    expect($taxReturn->ai_processing_status)->toBe('cancelled')
        ->and($taxReturn->total_income)->toBe('0.00');
});

test('job calculates tax liability and refund from extracted income', function () {
    Storage::fake('public');

    $geminiResponse = json_encode([
        'document_type' => 'w2',
        'confidence' => 0.95,
        'employer_name' => 'Acme Corp',
        'employer_ein' => '12-3456789',
        'income_items' => [
            [
                'type' => 'w2',
                'source_name' => 'Employment Income',
                'amount' => 100000.00,
                'federal_tax_withheld' => 22000.00,
                'state_tax_withheld' => 5000.00,
                'state' => 'CA',
            ],
        ],
        'deductions' => [
            ['category' => 'mortgage_interest', 'amount' => 10000.00, 'description' => 'Mortgage interest'],
        ],
        'taxpayer_info' => ['name' => 'Jane Doe'],
    ]);

    Gemini::fake([
        GenerateContentResponse::fake([
            'candidates' => [
                ['content' => ['parts' => [['text' => $geminiResponse]]]],
            ],
        ]),
    ]);

    $user = User::factory()->create(['role' => 'client']);
    $taxReturn = TaxReturn::create([
        'user_id' => $user->id,
        'tax_year' => 2025,
        'status' => TaxReturn::STATUS_SUBMITTED,
        'submitted_at' => now(),
        'total_income' => 0,
        'taxable_income' => 0,
        'tax_liability' => 0,
        'total_deductions' => 0,
        'total_credits' => 0,
        'amount_due' => 0,
        'refund_amount' => 0,
        'ai_processing_status' => 'pending',
    ]);

    $taxReturn->addMedia(UploadedFile::fake()->create('w2.pdf', 100, 'application/pdf'))
        ->toMediaCollection('documents');

    $job = new ProcessTaxDocuments($taxReturn->id);
    $job->handle(app(GeminiDocumentProcessor::class));

    $taxReturn->refresh();

    // 100k income - 10k deductions = 90k taxable
    expect((float) $taxReturn->total_income)->toBe(100000.00)
        ->and((float) $taxReturn->total_deductions)->toBe(10000.00)
        ->and((float) $taxReturn->taxable_income)->toBe(90000.00)
        ->and((float) $taxReturn->tax_liability)->toBeGreaterThan(0)
        ->and((float) $taxReturn->federal_tax_withheld)->toBe(22000.00);

    // With 22k withheld and ~14.7k liability on 90k taxable, should be a refund
    $expectedLiability = (float) $taxReturn->tax_liability;
    if ($expectedLiability < 22000) {
        expect((float) $taxReturn->refund_amount)->toBe(round(22000 - $expectedLiability, 2))
            ->and((float) $taxReturn->amount_due)->toBe(0.0);
    } else {
        expect((float) $taxReturn->amount_due)->toBe(round($expectedLiability - 22000, 2))
            ->and((float) $taxReturn->refund_amount)->toBe(0.0);
    }
});
