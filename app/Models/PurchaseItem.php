<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    use HasFactory;

    protected $fillable = ['purchase_id', 'item_id', 'quantity', 'unit_price', 'total_price'];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($purchaseItem) {
            // Update item current price
            $purchaseItem->item->update([
                'current_price' => $purchaseItem->unit_price
            ]);

            // Update stock
            $purchaseItem->item->updateStock($purchaseItem->quantity, 'in', $purchaseItem->purchase);
        });
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}