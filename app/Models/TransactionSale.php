<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionSale extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'total_qty'     => 'integer',
        'total_amount'  => 'double',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($transaction) {
            $items      =   $transaction->transactionBuyItems;

            foreach ($items as $item) {
                $item->delete();
            }
        });
    }

    public function transactionBuyItems()
    {
        return $this->hasMany(TransactionSaleItem::class, 'transaction_id');
    }
}
