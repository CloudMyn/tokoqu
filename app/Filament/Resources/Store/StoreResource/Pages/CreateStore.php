<?php

namespace App\Filament\Resources\Store\StoreResource\Pages;

use App\Filament\Resources\Store\StoreResource;
use App\Models\Owner;
use App\Models\Store;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateStore extends CreateRecord
{
    protected static string $resource = StoreResource::class;

    protected static ?string $buttonCreateLabel = null;

    protected static bool $canCreateAnother = false;

    protected static ?string $title = 'Tambahkan Toko';

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         $this->getCreateFormAction()->label('Simpan'),
    //         $this->getCancelFormAction()->label('Batalkan'),
    //     ];
    // }

    // protected function getFormActions(): array
    // {
    //     return [];
    // }


    protected function handleRecordCreation(array $data): Model
    {
        try {
            DB::beginTransaction();

            $store = new Store();

            $user = get_auth_user();

            $store->name    = $data['name'];
            $store->code    = $data['code'];
            $store->image   = $data['image'];
            $store->assets  = 0;
            $store->address = $data['address'];

            $store->owner()->associate($user->owner_store);

            $store->save();

            Notification::make()
                ->title('Berhasil Menyimpan Data')
                ->success()
                ->send();

            DB::commit();

            return $store;
        } catch (\Throwable $th) {

            DB::rollBack();

            Notification::make()
                ->title('Terjadi Kesalahan!')
                ->body($th->getMessage())
                ->danger()
                ->send();

            $this->halt();
        }
    }

    protected function getCreatedNotification(): ?Notification
    {
        return null;
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
