<?php

namespace App\Filament\Resources\StoreDashboard\AdjustStockResource\Pages;

use App\Filament\Resources\StoreDashboard\AdjustStockResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAdjustStocks extends ListRecords
{
    protected static string $resource = AdjustStockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
