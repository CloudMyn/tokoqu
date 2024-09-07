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

        static::creating(function (Debtor $debtor) {
            $debtor->store()->associate(get_context_store());
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
