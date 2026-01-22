<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxReturn extends Model
{
    protected $fillable = [
        'user_id',
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
        'completed_at'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'completed_at' => 'datetime',
        'total_income' => 'decimal:2',
        'taxable_income' => 'decimal:2',
        'tax_liability' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'total_credits' => 'decimal:2',
        'amount_due' => 'decimal:2',
        'refund_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
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
}
