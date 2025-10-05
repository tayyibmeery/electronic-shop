<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionRun extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'quantity_produced', 'production_date', 'notes', 'status'];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($productionRun) {
            // Deduct materials from stock
            foreach ($productionRun->product->materials as $material) {
                $totalRequired = $material->quantity_required * $productionRun->quantity_produced;
                $material->item->updateStock($totalRequired, 'out', $productionRun);
            }

            // Add finished product to stock
            $productionRun->product->updateStock($productionRun->quantity_produced, 'in', $productionRun);
        });
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}