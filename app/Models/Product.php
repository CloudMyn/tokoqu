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
    ];

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_code', 'code');
    }

}
