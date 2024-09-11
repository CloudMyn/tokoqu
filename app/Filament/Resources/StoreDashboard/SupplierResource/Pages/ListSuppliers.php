<?php

namespace App\Filament\Resources\StoreDashboard\SupplierResource\Pages;

use App\Filament\Resources\StoreDashboard\SupplierResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSuppliers extends ListRecords
{
    protected static string $resource = SupplierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
