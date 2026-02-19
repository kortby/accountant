<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentExtraction extends Model
{
    protected $fillable = [
        'tax_return_id',
        'media_id',
        'status',
        'document_type',
        'extracted_data',
        'confidence_score',
        'error_message',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'extracted_data' => 'array',
            'confidence_score' => 'decimal:2',
            'processed_at' => 'datetime',
        ];
    }

    public function taxReturn(): BelongsTo
    {
        return $this->belongsTo(TaxReturn::class);
    }
}
