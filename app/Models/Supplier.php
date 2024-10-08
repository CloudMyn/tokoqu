<?php

namespace App\Models;

use App\Traits\Ownership;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory, Ownership;

    protected $fillable = ['name', 'code'];

    protected static function booted()
    {
        static::creating(function ($supplier) {
            $supplier->store_code    =   get_context_store()->code;
        });
    }

    public function products()
    {
        return $this->hasMany(SupplierProduct::class);
    }
}
