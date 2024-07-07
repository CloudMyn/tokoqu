<?php

namespace App\Filament\Resources\StoreDashboard\AdjustStockResource\Pages;

use App\Filament\Resources\StoreDashboard\AdjustStockResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAdjustStock extends EditRecord
{
    protected static string $resource = AdjustStockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
