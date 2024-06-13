<?php

namespace App\Filament\Resources\Store\StoreResource\Pages;

use App\Filament\Resources\Store\StoreResource;
use App\Models\Store;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateStore extends CreateRecord
{
    protected static string $resource = StoreResource::class;

    protected static ?string $buttonCreateLabel = null;

    protected static bool $canCreateAnother = false;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function handleRecordCreation(array $data): Model
    {
        $store = new Store();

        $user = get_auth_user();

        $store->owner()->associate($user);
        $store->name    = $data['name'];
        $store->code    = $data['code'];
        $store->image   = $data['image'];
        $store->assets  = $data['assets'];
        $store->address = $data['address'];

        $store->save();

        Notification::make()
            ->title('Berhasil Menyimpan Data')
            ->success()
            ->send();

        return $store;
    }

    protected function uploadPhotoKTP(array $data): void
    {
    }

    protected function getCreatedNotification(): ?Notification
    {
        return null;
    }
}
