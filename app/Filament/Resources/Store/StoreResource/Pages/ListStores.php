<?php

namespace App\Filament\Resources\Store\StoreResource\Pages;

use App\Filament\Resources\Store\StoreResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStores extends ListRecords
{
    protected static string $resource = StoreResource::class;

    protected static ?string $title = 'Daftar Toko';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Toko'),
        ];
    }
}
