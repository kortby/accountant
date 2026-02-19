<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncomeSource extends Model
{
    protected $fillable = [
        'tax_return_id',
        'type',
        'source_name',
        'employer_name',
        'payer_ein',
        'amount',
        'federal_tax_withheld',
        'state_tax_withheld',
        'state',
        'description',
        'ai_extracted',
        'ai_confidence',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'federal_tax_withheld' => 'decimal:2',
            'state_tax_withheld' => 'decimal:2',
            'ai_extracted' => 'boolean',
            'ai_confidence' => 'decimal:2',
        ];
    }

    public function taxReturn(): BelongsTo
    {
        return $this->belongsTo(TaxReturn::class);
    }
}
