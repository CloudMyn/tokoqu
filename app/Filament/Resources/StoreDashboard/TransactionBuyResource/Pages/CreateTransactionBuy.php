<?php

namespace App\Filament\Resources\StoreDashboard\TransactionBuyResource\Pages;

use App\Filament\Resources\StoreDashboard\TransactionBuyResource;
use App\Models\TransactionBuy;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateTransactionBuy extends CreateRecord
{
    protected static string $resource = TransactionBuyResource::class;

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
        $user = get_auth_user();

        $owner  =   $user->owner_store;

        $store  =   $owner->store()->first();

        $data['store_code']     =   $store->code;
        $data['admin_name']     =   $user->name;
        $data['admin_id']       =   $user->id;

        return TransactionBuy::create($data);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Berhasil Menyimpan Data')
            ->success()
            ->send();
    }
}
