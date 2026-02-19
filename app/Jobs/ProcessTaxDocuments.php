<?php

namespace App\Jobs;

use App\Models\Deduction;
use App\Models\DocumentExtraction;
use App\Models\TaxReturn;
use App\Services\GeminiDocumentProcessor;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessTaxDocuments implements ShouldQueue
{
    use Queueable;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 2;

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout = 120;

    /**
     * Maximum file size in bytes to send to AI (5MB).
     * Larger files consume too many tokens and may timeout.
     */
    protected const MAX_AI_FILE_SIZE = 5 * 1024 * 1024;

    /**
     * MIME types supported for AI processing.
     */
    protected const SUPPORTED_MIME_TYPES = [
        'application/pdf',
        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/webp',
    ];

    /**
     * File extensions supported for AI processing.
     */
    protected const SUPPORTED_EXTENSIONS = ['pdf', 'jpg', 'jpeg', 'png', 'webp'];

    /**
     * Create a new job instance.
     */
    public function __construct(public int $taxReturnId) {}

    /**
     * Execute the job.
     */
    public function handle(GeminiDocumentProcessor $processor): void
    {
        $taxReturn = TaxReturn::find($this->taxReturnId);

        if (! $taxReturn) {
            Log::warning('ProcessTaxDocuments: Tax return not found', ['id' => $this->taxReturnId]);

            return;
        }

        // Don't overwrite a cancelled status
        if ($taxReturn->ai_processing_status === 'cancelled') {
            return;
        }

        $taxReturn->update(['ai_processing_status' => 'processing']);

        try {
            $mediaItems = $taxReturn->getMedia('documents');

            if ($mediaItems->isEmpty()) {
                $taxReturn->update([
                    'ai_processing_status' => 'completed',
                    'ai_processed_at' => now(),
                ]);

                return;
            }

            $allIncomeItems = [];
            $allDeductions = [];
            $totalFederalWithheld = 0;
            $maxDocuments = 10;
            $processed = 0;
            $successCount = 0;

            foreach ($mediaItems as $media) {
                // Check if processing was cancelled
                if ($taxReturn->fresh()->ai_processing_status === 'cancelled') {
                    Log::info('ProcessTaxDocuments: Processing cancelled by user', ['id' => $this->taxReturnId]);

                    break;
                }

                // Limit total documents processed per tax return
                if ($processed >= $maxDocuments) {
                    Log::info('ProcessTaxDocuments: Max document limit reached', [
                        'id' => $this->taxReturnId,
                        'limit' => $maxDocuments,
                    ]);

                    break;
                }

                // Skip files exceeding the size limit to avoid excessive token usage
                if ($media->size > self::MAX_AI_FILE_SIZE) {
                    DocumentExtraction::create([
                        'tax_return_id' => $taxReturn->id,
                        'media_id' => $media->id,
                        'status' => 'failed',
                        'error_message' => 'File too large for AI processing (max 5MB).',
                        'processed_at' => now(),
                    ]);

                    continue;
                }

                // Skip unsupported MIME types (e.g. doc, docx)
                $extension = strtolower(pathinfo($media->file_name, PATHINFO_EXTENSION));
                if (! in_array($media->mime_type, self::SUPPORTED_MIME_TYPES) && ! in_array($extension, self::SUPPORTED_EXTENSIONS)) {
                    DocumentExtraction::create([
                        'tax_return_id' => $taxReturn->id,
                        'media_id' => $media->id,
                        'status' => 'failed',
                        'error_message' => "Unsupported file type: {$media->mime_type}. AI supports PDF, JPEG, PNG, and WebP only.",
                        'processed_at' => now(),
                    ]);

                    continue;
                }

                $extraction = DocumentExtraction::create([
                    'tax_return_id' => $taxReturn->id,
                    'media_id' => $media->id,
                    'status' => 'processing',
                ]);

                try {
                    $result = $processor->processDocument($media);

                    $extraction->update([
                        'status' => 'completed',
                        'document_type' => $result['document_type'],
                        'extracted_data' => $result,
                        'confidence_score' => $result['confidence'],
                        'processed_at' => now(),
                    ]);

                    // Collect income items with metadata
                    foreach ($result['income_items'] as $item) {
                        $item['employer_name'] = $result['employer_name'];
                        $item['employer_ein'] = $result['employer_ein'];
                        $item['confidence'] = $result['confidence'];
                        $allIncomeItems[] = $item;
                    }

                    // Collect deductions
                    foreach ($result['deductions'] as $deduction) {
                        $deduction['confidence'] = $result['confidence'];
                        $allDeductions[] = $deduction;
                    }

                    $processed++;
                    $successCount++;
                } catch (\Exception $e) {
                    Log::error('ProcessTaxDocuments: Failed to process document', [
                        'media_id' => $media->id,
                        'error' => $e->getMessage(),
                    ]);

                    $extraction->update([
                        'status' => 'failed',
                        'error_message' => $e->getMessage(),
                        'processed_at' => now(),
                    ]);
                }
            }

            // If cancelled mid-processing, don't apply partial data
            if ($taxReturn->fresh()->ai_processing_status === 'cancelled') {
                return;
            }

            // If no documents were successfully processed, mark as failed
            if ($successCount === 0 && $mediaItems->isNotEmpty()) {
                $taxReturn->update([
                    'ai_processing_status' => 'failed',
                    'ai_processed_at' => now(),
                ]);

                return;
            }

            // Update existing income sources or create new ones from AI data
            $this->applyIncomeData($taxReturn, $allIncomeItems);

            // Create deductions from AI data
            $this->applyDeductionData($taxReturn, $allDeductions);

            // Recalculate totals
            $totalIncome = $taxReturn->incomeSources()->sum('amount');
            $totalDeductions = $taxReturn->deductions()->sum('amount');
            $federalWithheld = $taxReturn->incomeSources()->sum('federal_tax_withheld');
            $taxableIncome = max(0, $totalIncome - $totalDeductions);
            $taxLiability = $this->calculateTaxLiability($taxableIncome);

            // Calculate refund or amount due
            $netTax = $taxLiability - $federalWithheld;
            $refundAmount = $netTax < 0 ? abs($netTax) : 0;
            $amountDue = $netTax > 0 ? $netTax : 0;

            $taxReturn->update([
                'total_income' => $totalIncome,
                'total_deductions' => $totalDeductions,
                'taxable_income' => $taxableIncome,
                'tax_liability' => $taxLiability,
                'federal_tax_withheld' => $federalWithheld,
                'refund_amount' => $refundAmount,
                'amount_due' => $amountDue,
                'ai_processing_status' => 'completed',
                'ai_processed_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('ProcessTaxDocuments: Job failed', [
                'tax_return_id' => $this->taxReturnId,
                'error' => $e->getMessage(),
            ]);

            $taxReturn->update(['ai_processing_status' => 'failed']);

            throw $e;
        }
    }

    /**
     * Apply extracted income data to the tax return.
     *
     * Matches AI-extracted income items to existing income sources by type,
     * updating amounts and adding employer details. Creates new sources for
     * any AI-extracted types not already present.
     */
    protected function applyIncomeData(TaxReturn $taxReturn, array $incomeItems): void
    {
        $existingSources = $taxReturn->incomeSources()->get()->keyBy('type');

        foreach ($incomeItems as $item) {
            $type = $item['type'] ?? 'other';

            if ($existingSources->has($type)) {
                // Update existing source with AI data
                $source = $existingSources->get($type);
                $source->update([
                    'amount' => $item['amount'],
                    'employer_name' => $item['employer_name'] ?? $source->employer_name,
                    'payer_ein' => $item['employer_ein'] ?? $source->payer_ein,
                    'federal_tax_withheld' => $item['federal_tax_withheld'] ?? 0,
                    'state_tax_withheld' => $item['state_tax_withheld'] ?? 0,
                    'state' => $item['state'] ?? $source->state,
                    'ai_extracted' => true,
                    'ai_confidence' => $item['confidence'] ?? 0,
                ]);
                // Remove from collection so we don't match it again
                $existingSources->forget($type);
            } else {
                // Create new income source from AI
                $taxReturn->incomeSources()->create([
                    'type' => $type,
                    'source_name' => $item['source_name'] ?? 'AI Extracted Income',
                    'amount' => $item['amount'],
                    'employer_name' => $item['employer_name'] ?? null,
                    'payer_ein' => $item['employer_ein'] ?? null,
                    'federal_tax_withheld' => $item['federal_tax_withheld'] ?? 0,
                    'state_tax_withheld' => $item['state_tax_withheld'] ?? 0,
                    'state' => $item['state'] ?? null,
                    'ai_extracted' => true,
                    'ai_confidence' => $item['confidence'] ?? 0,
                ]);
            }
        }
    }

    /**
     * Apply extracted deduction data to the tax return.
     */
    protected function applyDeductionData(TaxReturn $taxReturn, array $deductions): void
    {
        foreach ($deductions as $deduction) {
            $taxReturn->deductions()->create([
                'category' => $deduction['category'] ?? 'other',
                'amount' => $deduction['amount'],
                'description' => $deduction['description'] ?? '',
            ]);
        }
    }

    /**
     * Calculate estimated federal tax liability using 2025 US tax brackets (single filing).
     *
     * This is an estimate — the accountant should review and adjust.
     */
    protected function calculateTaxLiability(float $taxableIncome): float
    {
        $brackets = [
            [11925, 0.10],
            [48475 - 11925, 0.12],
            [103350 - 48475, 0.22],
            [197300 - 103350, 0.24],
            [250525 - 197300, 0.32],
            [626350 - 250525, 0.35],
            [PHP_FLOAT_MAX, 0.37],
        ];

        $tax = 0;
        $remaining = $taxableIncome;

        foreach ($brackets as [$width, $rate]) {
            if ($remaining <= 0) {
                break;
            }
            $taxable = min($remaining, $width);
            $tax += $taxable * $rate;
            $remaining -= $taxable;
        }

        return round($tax, 2);
    }
}
