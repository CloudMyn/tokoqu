<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionBuy extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'total_qty' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($transaction) {

            add_store_asset(
                store: $transaction->store,
                title: 'Transaksi Pembelian #' . $transaction->id,
                message: 'Transaksi pembelian : ' . ubah_angka_int_ke_rupiah($transaction->total_amount) . " ( " . $transaction->total_qty . " )",
                type: 'out',
                amount: $transaction->total_amount,
            );
        });


        static::deleting(function ($transaction) {
            $items      =   $transaction->transactionBuyItems;

            foreach ($items as $item) {
                $item->delete();
            }

            delete_store_asset(title: 'Transaksi Pembelian #' . $transaction->id);
        });
    }

    public function transactionBuyItems()
    {
        return $this->hasMany(TransactionBuyItem::class, 'transaction_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_code', 'code');
    }
}
