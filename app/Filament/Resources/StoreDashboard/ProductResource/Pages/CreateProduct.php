<?php

namespace App\Filament\Resources\StoreDashboard\ProductResource\Pages;

use App\Filament\Resources\StoreDashboard\ProductResource;
use App\Models\Product;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

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
        $store  =   get_context_store();

        $product = new Product();

        $product->store()->associate($store);

        $product->name          = $data['name'];
        $product->sku           = strtoupper($data['sku']);
        $product->image         = $data['image'] ?? null;
        $product->stock         = $data['stock'];
        $product->sale_price    = ubah_angka_rupiah_ke_int($data['sale_price']);
        $product->product_cost  = ubah_angka_rupiah_ke_int($data['product_cost']);
        $product->fraction      = $data['fraction'];
        $product->unit          = $data['unit'];
        $product->delivery_fee  = ubah_angka_rupiah_ke_int($data['delivery_fee']);

        $product->save();

        return $store;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Berhasil Menyimpan Data')
            ->success()
            ->send();
    }
}
