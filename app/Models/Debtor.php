<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Debtor extends Model
{
    use HasFactory;

    protected $guarded = [];


    public function transaction()
    {
        return $this->belongsTo(TransactionSale::class, 'transaction_id');
    }
}
