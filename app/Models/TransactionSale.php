<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionSale extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $with = ['transactionSaleItems', 'store', 'debt'];

    protected $casts = [
        'total_qty'     => 'integer',
        'total_profit'  => 'integer',
        'total_amount'  => 'integer',
    ];

    protected static function booted()
    {
        static::created(function ($transaction) {});

        static::deleting(function ($transaction) {
            delete_store_asset(title: 'Transaksi Penjualan #' . $transaction->id);

            $transaction->debt()->delete();
            $transaction->transactionSaleItems()->delete();
        });
    }

    public function transactionSaleItems()
    {
        return $this->hasMany(TransactionSaleItem::class, 'transaction_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_code', 'code');
    }

    public function debt()
    {
        return $this->hasOne(Debtor::class, 'transaction_id');
    }
}
