<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionBuyItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'total_qty' =>  'integer'
    ];

    protected static function booted()
    {
        static::deleting(function ($transactionBuyItem) {
            $product = $transactionBuyItem->product;

            $product->update([
                'stock' =>  $product->stock - intval($transactionBuyItem->total_qty),
            ]);
        });
    }

    public function transaction()
    {
        return $this->belongsTo(TransactionBuy::class, 'transaction_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_code', 'code');
    }
}
