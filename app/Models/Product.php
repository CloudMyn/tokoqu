<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'stock'         =>  'integer',
        'fraction'      =>  'integer',
        'product_cost'  =>  'double',
        'sale_price'    =>  'double',
    ];

    protected static function booted()
    {
        static::created(function ($product) {
            // when product created

            add_store_asset(
                store: $product->store,
                title: 'Penambahan Produk #' . $product->id,
                message: 'Produk yang di tambahkan : ' . $product->name . " ( " . $product->stock . " )",
                type: 'out',
                amount: intval($product->product_cost * $product->stock),
            );
        });

        static::deleting(function ($product) {

            add_store_asset(
                store: $product->store,
                title: 'Penghapusan Produk #' . $product->id,
                message: 'Produk yang di hapus : ' . $product->name . " ( " . $product->stock . " )",
                type: 'in',
                amount: intval($product->product_cost * $product->stock),
            );
        });
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_code', 'code');
    }

    public function transaction_sale_items()
    {
        return $this->hasMany(TransactionSaleItem::class, 'product_id', 'id');
    }
}
