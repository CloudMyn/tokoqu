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

            $store  =   get_context_store();

            $product->store()->associate($store);

            $product->name          = $data['name'];
            $product->sku           = strtoupper($data['sku']);
            $product->image         = $data['image'];
            $product->sale_price    = ubah_angka_rupiah_ke_int($data['sale_price']);
            $product->product_cost  = ubah_angka_rupiah_ke_int($data['product_cost']);
            $product->fraction      = $data['fraction'];
            $product->unit          = $data['unit'];
            $product->delivery_fee  = ubah_angka_rupiah_ke_int($data['delivery_fee']);

            $product->save();

            DB::commit();

            return $product;
        } catch (\Throwable $th) {

            DB::rollBack();

            throw $th;
        }
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Berhasil menyimpan perubahan')
            ->success()
            ->send();
    }
}
