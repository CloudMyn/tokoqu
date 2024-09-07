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
            $debtor->asset->delete();
        });

        static::creating(function (Debtor $debtor) {
            $store  =   get_context_store();

            $debtor->store()->associate($store);
        });

        static::saved(function (Debtor $debtor) {
            if ($debtor->status == 'paid') {
                $debtor->asset->update([
                    'type'    =>  'in'
                ]);

                sync_store_assets();
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

    public function asset()
    {
        return $this->belongsTo(StoreAsset::class, 'asset_id', 'id');
    }
}
