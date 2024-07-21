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

        static::created(function ($transaction) {

            add_store_asset(
                store: $transaction->store,
                title: 'Transaksi Penjualan #' . $transaction->id,
                message: "Transaksi penjualan ID #{$transaction->id} : Rp. " .  ubah_angka_int_ke_rupiah($transaction->total_amount) . " ( " . $transaction->total_qty . " )",
                type: 'in',
                amount: $transaction->total_amount,
            );
        });

        static::deleting(function ($transaction) {
            $items      =   $transaction->transactionBuyItems;

            foreach ($items as $item) {
                $item->delete();
            }

            delete_store_asset(title: 'Transaksi Penjualan #' . $transaction->id);
        });
    }

    public function transactionBuyItems()
    {
        return $this->hasMany(TransactionSaleItem::class, 'transaction_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_code', 'code');
    }
}
