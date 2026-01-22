<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncomeSource extends Model
{
    protected $fillable = [
        'tax_return_id',
        'type',
        'source_name',
        'amount',
        'description'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function taxReturn()
    {
        return $this->belongsTo(TaxReturn::class);
    }
}
