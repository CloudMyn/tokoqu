<?php

namespace App\Filament\Resources\StoreDashboard\StoreAssetResource\Pages;

use App\Filament\Resources\StoreDashboard\StoreAssetResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateStoreAsset extends CreateRecord
{
    protected static string $resource = StoreAssetResource::class;

    protected static ?string $title = 'Input Kas';

    protected static bool $canCreateAnother = false;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['store_code'] = get_context_store()->code;

        return $data;
    }
}
