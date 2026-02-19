<?php

namespace App\Services;

use Gemini\Data\Blob;
use Gemini\Enums\MimeType;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\Log;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class GeminiDocumentProcessor
{
    /**
     * The Gemini model to use for document processing.
     */
    protected string $model = 'gemini-2.0-flash';

    /**
     * Process a single document and extract structured tax data.
     *
     * @return array{document_type: string, confidence: float, income_items: array, deductions: array, taxpayer_info: array}
     */
    public function processDocument(Media $media): array
    {
        $filePath = $media->getPath();

        if (! file_exists($filePath)) {
            throw new \RuntimeException("File not found: {$filePath}");
        }

        $fileContents = file_get_contents($filePath);
        $mimeType = $this->getMimeType($media->mime_type);

        $result = Gemini::generativeModel(model: $this->model)
            ->generateContent([
                new Blob(
                    mimeType: $mimeType,
                    data: base64_encode($fileContents),
                ),
                $this->buildPrompt(),
            ]);

        $text = $result->text();

        return $this->parseResponse($text);
    }

    /**
     * Build the extraction prompt for Gemini.
     */
    protected function buildPrompt(): string
    {
        return <<<'PROMPT'
You are a tax document extraction expert. Analyze this document and extract all financial information.

Return ONLY a valid JSON object with this exact structure (no markdown, no code blocks, just raw JSON):

{
    "document_type": "w2|1099_nec|1099_int|1099_div|1099_k|1099_r|1098|receipt|other",
    "confidence": 0.95,
    "employer_name": "Company Name or Payer Name",
    "employer_ein": "XX-XXXXXXX",
    "income_items": [
        {
            "type": "w2|1099_nec|1099_int|1099_div|1099_k|business|rental|retirement|other",
            "source_name": "Description of this income",
            "amount": 50000.00,
            "federal_tax_withheld": 7500.00,
            "state_tax_withheld": 2500.00,
            "state": "CA"
        }
    ],
    "deductions": [
        {
            "category": "mortgage_interest|property_tax|charitable|medical|student_loan|business_expense|other",
            "amount": 12000.00,
            "description": "Description of deduction"
        }
    ],
    "taxpayer_info": {
        "name": "John Doe",
        "address": "123 Main St",
        "city": "Los Angeles",
        "state": "CA",
        "zip": "90001"
    }
}

Rules:
- For W-2: Extract wages (Box 1), federal tax withheld (Box 2), state wages (Box 16), state tax withheld (Box 17), employer name, EIN
- For 1099-NEC: Extract nonemployee compensation (Box 1), payer name
- For 1099-INT: Extract interest income (Box 1), payer name
- For 1099-DIV: Extract ordinary dividends (Box 1a), qualified dividends (Box 1b), payer name
- For 1099-K: Extract gross amount (Box 1a), payer name
- For 1099-R: Extract gross distribution (Box 1), taxable amount (Box 2a)
- For 1098: Extract mortgage interest paid (Box 1) as a deduction
- For receipts: Extract any deductible expenses
- Set amounts to 0 if not found. Omit empty arrays. Set confidence between 0 and 1.
- If the document is not a recognizable tax form, set document_type to "other" and extract whatever financial info is visible.
PROMPT;
    }

    /**
     * Parse the Gemini response text into structured data.
     */
    protected function parseResponse(string $text): array
    {
        // Strip markdown code blocks if present
        $text = preg_replace('/^```(?:json)?\s*\n?/m', '', $text);
        $text = preg_replace('/\n?```\s*$/m', '', $text);
        $text = trim($text);

        $data = json_decode($text, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::warning('GeminiDocumentProcessor: Failed to parse JSON response', [
                'error' => json_last_error_msg(),
                'raw_text' => substr($text, 0, 500),
            ]);

            return $this->emptyResult();
        }

        // Normalize and validate the response
        return [
            'document_type' => $data['document_type'] ?? 'other',
            'confidence' => min(1.0, max(0.0, (float) ($data['confidence'] ?? 0.5))),
            'employer_name' => $data['employer_name'] ?? null,
            'employer_ein' => $data['employer_ein'] ?? null,
            'income_items' => $this->normalizeIncomeItems($data['income_items'] ?? []),
            'deductions' => $this->normalizeDeductions($data['deductions'] ?? []),
            'taxpayer_info' => $data['taxpayer_info'] ?? [],
        ];
    }

    /**
     * Normalize income items from the AI response.
     */
    protected function normalizeIncomeItems(array $items): array
    {
        $validTypes = ['w2', '1099_nec', '1099_int', '1099_div', '1099_k', 'business', 'rental', 'retirement', 'other'];

        return array_map(function (array $item) use ($validTypes) {
            $type = $item['type'] ?? 'other';

            return [
                'type' => in_array($type, $validTypes) ? $type : 'other',
                'source_name' => $item['source_name'] ?? 'Unknown Source',
                'amount' => max(0, (float) ($item['amount'] ?? 0)),
                'federal_tax_withheld' => max(0, (float) ($item['federal_tax_withheld'] ?? 0)),
                'state_tax_withheld' => max(0, (float) ($item['state_tax_withheld'] ?? 0)),
                'state' => $item['state'] ?? null,
            ];
        }, $items);
    }

    /**
     * Normalize deductions from the AI response.
     */
    protected function normalizeDeductions(array $items): array
    {
        return array_map(function (array $item) {
            return [
                'category' => $item['category'] ?? 'other',
                'amount' => max(0, (float) ($item['amount'] ?? 0)),
                'description' => $item['description'] ?? '',
            ];
        }, $items);
    }

    /**
     * Return an empty result structure.
     */
    protected function emptyResult(): array
    {
        return [
            'document_type' => 'other',
            'confidence' => 0.0,
            'employer_name' => null,
            'employer_ein' => null,
            'income_items' => [],
            'deductions' => [],
            'taxpayer_info' => [],
        ];
    }

    /**
     * Map file MIME type string to Gemini MimeType enum.
     */
    protected function getMimeType(string $mimeType): MimeType
    {
        return match ($mimeType) {
            'image/jpeg', 'image/jpg' => MimeType::IMAGE_JPEG,
            'image/png' => MimeType::IMAGE_PNG,
            'image/webp' => MimeType::IMAGE_WEBP,
            'application/pdf' => MimeType::APPLICATION_PDF,
            default => MimeType::APPLICATION_PDF,
        };
    }
}
