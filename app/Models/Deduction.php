<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deduction extends Model
{
    protected $fillable = [
        'tax_return_id',
        'category',
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
