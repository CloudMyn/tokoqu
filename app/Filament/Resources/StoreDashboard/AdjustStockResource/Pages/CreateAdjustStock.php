<?php

namespace App\Filament\Resources\StoreDashboard\AdjustStockResource\Pages;

use App\Filament\Resources\StoreDashboard\AdjustStockResource;
use App\Models\AdjustStock;
use App\Models\Product;
use App\Models\StoreAsset;
use App\Traits\Ownership;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateAdjustStock extends CreateRecord
{
    use Ownership;

    protected static string $resource = AdjustStockResource::class;

    protected static ?string $title = 'Input Adjust';

    protected static ?string $buttonCreateLabel = null;

    protected static bool $canCreateAnother = false;

    protected function handleRecordCreation(array $data): Model
    {
        try {
            DB::beginTransaction();

            $user = get_auth_user();

            $owner  =   $user->owner_store;

            $store  =   $owner->store()->first();

            $product    =   Product::findOrFail($data['product_id']);

            if ($data['type'] === 'minus' && $product->stock < intval($data['qty'])) {
                throw new \Exception("Stok barang tidak cukup");
            }

            $adjust_stock   =   new AdjustStock();

            $adjust_stock->product()->associate($product);
            $adjust_stock->admin()->associate($user);
            $adjust_stock->store()->associate($store);

            $total_amount   =   $product->product_cost * intval($data['qty']);

            $adjust_stock->message          =   $data['message'];
            $adjust_stock->total_amount     =   doubleval($total_amount);
            $adjust_stock->total_qty        =   $data['qty'];
            $adjust_stock->type             =   $data['type'];
            $adjust_stock->admin_name       =   $user->name;

            $adjust_stock->save();

            if ($data['type'] == 'plus') {
                $product->stock += intval($data['qty']);
            } else {
                $product->stock -= intval($data['qty']);
            }

            $product->save();

            // input kase
            $asset    =   new StoreAsset();

            $asset->store()->associate($store);

            $asset->type    =   $data['type'] === 'plus' ? 'in' : 'out';
            $asset->amount  =   doubleval($total_amount);
            $asset->title   =   "Adjust Stock #" . $adjust_stock->id;
            $asset->message =   "Adjust Stock : " . $data['message'];

            $asset->save();

            DB::commit();

            Notification::make()
                ->title('Berhasil Menyimpan Data')
                ->success()
                ->send();

            return $adjust_stock;
        } catch (\Throwable $th) {
            DB::rollBack();

            Notification::make()
                ->warning()
                ->title('Terjadi kesalahan!')
                ->body($th->getMessage())
                ->send();

            $this->halt();
        }
    }

    protected function getCreatedNotification(): ?Notification
    {
        return null;
    }
}
