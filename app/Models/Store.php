<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'integer'   =>  'assets',
        'double'    =>  'total_amount',
        'double'    =>  'total_profit',
        'integer'   =>  'total_qty',
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class, 'store_code', 'code');
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id', 'id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'store_code', 'code');
    }

    public function transaction_sales()
    {
        return $this->hasMany(TransactionSale::class, 'store_code', 'code');
    }

    public function transaction_buys()
    {
        return $this->hasMany(TransactionBuy::class, 'store_code', 'code');
    }

    public function store_assets()
    {
        return $this->hasMany(StoreAsset::class, 'store_code', 'code');
    }

    public function adjusts_stock()
    {
        return $this->hasMany(AdjustStock::class, 'store_code', 'code');
    }

    public function getSalesAndProfits($period = 'monthly')
    {
        $query = $this->transaction_sales()
            ->selectRaw('SUM(total_amount) as total_amount, SUM(total_qty) as total_qty, SUM(total_profit) as total_profit, COUNT(*) as sales_count');

        switch ($period) {
            case 'daily':
                $query->selectRaw('DATE(created_at) as period')
                    ->groupByRaw('DATE(created_at)');
                break;
            case 'weekly':
                $query->selectRaw('YEARWEEK(created_at) as period')
                    ->groupByRaw('YEARWEEK(created_at)');
                break;
            case 'monthly':
                $query->selectRaw('MONTH(created_at) as period')
                    ->groupByRaw('MONTH(created_at)');
                break;
            case 'yearly':
                $query->selectRaw('YEAR(created_at) as period')
                    ->groupByRaw('YEAR(created_at)');
                break;
        }

        return $query->orderBy('period')
            ->get()
            ->keyBy('period')
            ->map(function ($row) {
                return [
                    'sales_count'  => doubleval($row->sales_count),
                    'trx_amount'   => doubleval($row->total_amount),
                    'total_qty'    => doubleval($row->total_qty),
                    'total_profit' => doubleval($row->total_profit),
                ];
            })
            ->toArray();
    }

    public function getBuysAndCosts($period = 'monthly')
    {
        $query = $this->transaction_buys()
            ->selectRaw('SUM(total_cost) as total_cost, SUM(total_qty) as total_qty, COUNT(*) as buys_count');

        switch ($period) {
            case 'daily':
                $query->selectRaw('DATE(created_at) as period')
                    ->groupByRaw('DATE(created_at)');
                break;
            case 'weekly':
                $query->selectRaw('YEARWEEK(created_at) as period')
                    ->groupByRaw('YEARWEEK(created_at)');
                break;
            case 'monthly':
                $query->selectRaw('MONTH(created_at) as period')
                    ->groupByRaw('MONTH(created_at)');
                break;
            case 'yearly':
                $query->selectRaw('YEAR(created_at) as period')
                    ->groupByRaw('YEAR(created_at)');
                break;
        }

        return $query->orderBy('period')
            ->get()
            ->keyBy('period')
            ->map(function ($row) {
                return [
                    'buys_count' => doubleval($row->buys_count),
                    'total_cost' => doubleval($row->total_cost),
                    'total_qty'  => doubleval($row->total_qty),
                ];
            })
            ->toArray();
    }
}
