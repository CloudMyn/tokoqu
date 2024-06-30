<?php

namespace App\Filament\Resources\StoreDashboard\TransactionBuyResource\Pages;

use App\Filament\Resources\StoreDashboard\TransactionBuyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransactionBuys extends ListRecords
{
    protected static string $resource = TransactionBuyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
