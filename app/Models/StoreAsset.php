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
            sync_store_assets();
        });

        static::deleting(function ($storeAsset) {
            sync_store_assets();
        });
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_code', 'code');
    }
}
