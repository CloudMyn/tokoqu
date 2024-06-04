<?php

namespace App\Filament\Resources\OwnerStoreResource\Pages;

use App\Filament\Resources\OwnerStoreResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOwnerStore extends EditRecord
{
    protected static string $resource = OwnerStoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
