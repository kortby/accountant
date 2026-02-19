<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class TaxReturn extends Model implements HasMedia
{
    use InteractsWithMedia;

    // Valid status values
    public const STATUS_DRAFT = 'draft';

    public const STATUS_SUBMITTED = 'submitted';

    public const STATUS_ASSIGNED = 'assigned';

    public const STATUS_UNDER_REVIEW = 'under_review';

    public const STATUS_NEEDS_ACTION = 'needs_action';

    public const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'user_id',
        'accountant_id',
        'tax_year',
        'status',
        'total_income',
        'taxable_income',
        'tax_liability',
        'total_deductions',
        'total_credits',
        'amount_due',
        'refund_amount',
        'submitted_at',
        'assigned_at',
        'reviewed_at',
        'completed_at',
        'accountant_notes',
        'ai_processing_status',
        'ai_processed_at',
        'federal_tax_withheld',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'assigned_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'completed_at' => 'datetime',
        'ai_processed_at' => 'datetime',
        'total_income' => 'decimal:2',
        'taxable_income' => 'decimal:2',
        'tax_liability' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'total_credits' => 'decimal:2',
        'amount_due' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'federal_tax_withheld' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function accountant()
    {
        return $this->belongsTo(User::class, 'accountant_id');
    }

    public function incomeSources()
    {
        return $this->hasMany(IncomeSource::class);
    }

    public function deductions()
    {
        return $this->hasMany(Deduction::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function documentExtractions()
    {
        return $this->hasMany(DocumentExtraction::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('documents')
            ->useDisk('public'); // Or 's3' for production
    }
}
