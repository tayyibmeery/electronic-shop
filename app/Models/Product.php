<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'description', 'selling_price', 'stock_quantity'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            $product->code = 'PROD-' . strtoupper(substr($product->name, 0, 3)) . '-' . str_pad(Product::count() + 1, 4, '0', STR_PAD_LEFT);
        });
    }

    public function materials()
    {
        return $this->hasMany(ProductMaterial::class);
    }

    public function productionRuns()
    {
        return $this->hasMany(ProductionRun::class);
    }

    public function stockLedgers()
    {
        return $this->morphMany(StockLedger::class, 'stockable');
    }

    public function updateStock($quantity, $type, $reference = null)
    {
        $oldStock = $this->stock_quantity;

        if ($type === 'in') {
            $this->stock_quantity += $quantity;
        } else {
            $this->stock_quantity -= $quantity;
        }

        $this->save();

        // Record in stock ledger
        StockLedger::create([
            'stockable_type' => self::class,
            'stockable_id' => $this->id,
            'type' => $type,
            'quantity' => $quantity,
            'balance_after' => $this->stock_quantity,
            'reference_type' => $reference ? get_class($reference) : null,
            'reference_id' => $reference ? $reference->id : null,
            'notes' => "Stock {$type} transaction"
        ]);
    }

    public function getProductionCost()
    {
        $totalCost = 0;
        foreach ($this->materials as $material) {
            $totalCost += $material->item->current_price * $material->quantity_required;
        }
        return $totalCost;
    }
}