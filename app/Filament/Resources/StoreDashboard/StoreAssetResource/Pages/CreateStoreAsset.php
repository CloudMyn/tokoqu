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

        $store  =   get_context_store();

        $assets =   $store->assets;

        if ($data['type'] == 'in') {
            $new_assets =   $assets + $data['amount'];
        } else if ($data['type'] == 'out') {
            $new_assets =   $assets - $data['amount'];
        } else {
            $new_assets =   $assets;
        }

        $store->update([
            'assets' => $new_assets,
        ]);

        return $data;
    }
}
