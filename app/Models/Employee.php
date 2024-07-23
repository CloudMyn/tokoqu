<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $guarded = [];


    protected static function booted()
    {
        static::deleting(function ($employee) {
            $employee->user()->delete();
        });
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_code', 'code');
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_code', 'code');
    }
}
