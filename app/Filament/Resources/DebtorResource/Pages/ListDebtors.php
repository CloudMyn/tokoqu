<?php

namespace App\Filament\Resources\DebtorResource\Pages;

use App\Filament\Resources\DebtorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDebtors extends ListRecords
{
    protected static string $resource = DebtorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
