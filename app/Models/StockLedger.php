<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockLedger extends Model
{
    use HasFactory;

    protected $fillable = [
        'stockable_type', 'stockable_id', 'type', 'quantity',
        'balance_after', 'reference_type', 'reference_id', 'notes'
    ];

    public function stockable()
    {
        return $this->morphTo();
    }
}