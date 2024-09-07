<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Debtor extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'amount'    =>  'integer',
        'paid'      =>  'integer'
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function (Debtor $debtor) {
            $store  =   get_context_store();

            $store->update([
                'assets'    =>  $store->assets + $debtor->amount
            ]);
        });

        static::creating(function (Debtor $debtor) {
            $store  =   get_context_store();

            $debtor->store()->associate($store);

            $store->update([
                'assets'    =>  $store->assets - $debtor->amount
            ]);
        });

        static::saved(function (Debtor $debtor) {
            if ($debtor->status == 'paid') {
                $store  =   get_context_store();

                $store->update([
                    'assets'    =>  $store->assets + $debtor->amount
                ]);
            }
        });
    }

    public function transaction()
    {
        return $this->belongsTo(TransactionSale::class, 'transaction_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_code', 'code');
    }
}
