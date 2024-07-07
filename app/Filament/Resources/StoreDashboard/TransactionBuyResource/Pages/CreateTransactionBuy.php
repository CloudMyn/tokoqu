<?php

namespace App\Filament\Resources\StoreDashboard\TransactionBuyResource\Pages;

use App\Filament\Resources\StoreDashboard\TransactionBuyResource;
use App\Models\Store;
use App\Models\TransactionBuy;
use App\Models\TransactionBuyItem;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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
        try {
            DB::beginTransaction();

            $user = get_auth_user();

            $owner  =   $user->owner_store;

            $store  =   $owner->store()->first();

            $products   =   $data['products'];

            unset($data['products']);

            $data['store_code']     =   $store->code;
            $data['admin_name']     =   $user->name;
            $data['admin_id']       =   $user->id;
            $data['total_qty'] = array_reduce($products, function ($acc, $item) {
                return $acc + intval($item['product_qty']);
            }, 0);

            $transaction = TransactionBuy::create($data);

            foreach ($products as $product) {
                $product_model  =    $store->products()->find($product['product_id']);

                $product_cost   =   $product['product_cost'];

                $product_trx_model  =   new TransactionBuyItem();

                $product_trx_model->transaction()->associate($transaction);
                $product_trx_model->product()->associate($product_model);
                $product_trx_model->store()->associate($store);

                $product_trx_model->product_sku     =   $product_model->sku;
                $product_trx_model->product_name    =   $product_model->name;
                $product_trx_model->product_cost    =   $product_cost;
                $product_trx_model->total_qty       =   intval($product['product_qty']);

                $product_trx_model->save();

                $product_model->update([
                    'stock'     =>  $product_model->stock + intval($product['product_qty']),
                    'supplier'  =>  $data['supplier'],
                ]);
            }

            DB::commit();

            return $transaction;
        } catch (\Throwable $th) {

            DB::rollBack();

            throw $th;
        }
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Berhasil Menyimpan Data')
            ->success()
            ->send();
    }
}
