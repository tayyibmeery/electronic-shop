<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'description', 'category', 'unit', 'current_price', 'stock_quantity', 'min_stock'];

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function productMaterials()
    {
        return $this->hasMany(ProductMaterial::class);
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
}