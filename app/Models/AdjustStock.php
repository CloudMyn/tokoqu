<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdjustStock extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected   $casts  =   [
        'id'        =>  'integer',
        'total_qty' =>  'integer',
        'total_amount'  =>  'integer'
    ];

    protected static function booted()
    {

        static::creating(function ($model) {
            $model->store()->associate(get_context_store());
        });

        static::deleting(function ($adjustStock) {

            $product    =   $adjustStock->product;

            if ($adjustStock->type == 'plus') {
                $product->update([
                    'stock' =>  $product->stock - intval($adjustStock->total_qty),
                ]);
            } else {
                $product->update([
                    'stock' =>  $product->stock + intval($adjustStock->total_qty),
                ]);
            }

            StoreAsset::where('title', '=', 'Adjust Stock #'. $adjustStock->id)->delete();
        });
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_code', 'code');
    }
}
