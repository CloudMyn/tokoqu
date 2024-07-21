<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreAsset extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function booted()
    {
        static::created(function ($storeAsset) {

            $store  =   $storeAsset->store;

            if ($storeAsset->type == 'in') {
                $store->update([
                    'assets'    =>  $store->assets + $storeAsset->amount
                ]);
            } else {
                $store->update([
                    'assets'    =>  $store->assets - $storeAsset->amount
                ]);
            }
        });

        static::deleting(function ($storeAsset) {

            $store  =   $storeAsset->store;

            if ($storeAsset->type == 'in') {
                $store->update([
                    'assets'    =>  $store->assets - $storeAsset->amount
                ]);
            } else {
                $store->update([
                    'assets'    =>  $store->assets + $storeAsset->amount
                ]);
            }
        });
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_code', 'code');
    }
}
