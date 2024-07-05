<?php

namespace App\Filament\Resources\StoreDashboard\ProductResource\Pages;

use App\Filament\Resources\StoreDashboard\ProductResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $product, array $data): Model
    {
        try {

            DB::beginTransaction();

            $user = get_auth_user();

            $owner  =   $user->owner_store;

            $store  =   $owner->store()->first();

            $product->store()->associate($store);

            $product->name          = $data['name'];
            $product->supplier      = $data['supplier'];
            $product->sku           = strtoupper($data['sku']);
            $product->image         = $data['image'];
            $product->stock         = intval($data['stock']);
            $product->sale_price    = ubah_angka_rupiah_ke_int($data['sale_price']);
            $product->product_cost  = ubah_angka_rupiah_ke_int($data['product_cost']);
            $product->fraction      = $data['fraction'];
            $product->unit          = $data['unit'];

            $product->save();

            DB::commit();

            return $product;
        } catch (\Throwable $th) {

            DB::rollBack();

            throw $th;
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Berhasil menyimpan perubahan')
            ->success()
            ->send();
    }
}
