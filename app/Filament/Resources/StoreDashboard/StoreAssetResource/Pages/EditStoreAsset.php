<?php

namespace App\Filament\Resources\StoreDashboard\StoreAssetResource\Pages;

use App\Filament\Resources\StoreDashboard\StoreAssetResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStoreAsset extends EditRecord
{
    protected static string $resource = StoreAssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
