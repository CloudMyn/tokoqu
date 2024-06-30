<?php

namespace App\Filament\Resources\StoreDashboard\TransactionSaleResource\Pages;

use App\Filament\Resources\StoreDashboard\TransactionSaleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTransactionSale extends EditRecord
{
    protected static string $resource = TransactionSaleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
