<?php

namespace App\Filament\Resources\StoreDashboard\TransactionSaleResource\Pages;

use App\Filament\Resources\StoreDashboard\TransactionSaleResource;
use App\Models\TransactionSale;
use App\Models\TransactionSaleItem;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateTransactionSale extends CreateRecord
{
    protected static string $resource = TransactionSaleResource::class;

    protected static ?string $buttonCreateLabel = null;

    protected static bool $canCreateAnother = false;

    protected function handleRecordCreation(array $data): Model
    {
        try {
            DB::beginTransaction();

            $user = get_auth_user();

            $owner  =   $user->owner_store;

            $store  =   $owner->store()->first();

            $products       =   $data['products'];
            $trx_discount   =   doubleval($data['discount']) / count($products);

            unset($data['products']);
            unset($data['discount']);

            $data['store_code']     =   $store->code;
            $data['admin_name']     =   $user->name;
            $data['admin_id']       =   $user->id;

            $data['total_amount']   =   0;
            $data['total_profit']   =   0;
            $data['total_qty']      = array_reduce($products, function ($acc, $item) {
                return $acc + intval($item['product_qty']);
            }, 0);

            $product_trx_models = [];

            foreach ($products as $product) {
                $product_model  =    $store->products()->find($product['product_id']);

                $product_discount   =   doubleval($product['product_discount']);

                $product_trx_model  =   new TransactionSaleItem();

                $product_trx_model->product()->associate($product_model);
                $product_trx_model->store()->associate($store);

                $product_trx_model->sale_product($product_model, $product['product_qty'], $product_discount + $trx_discount);

                $product_trx_model->product_sku     =   $product_model->sku;
                $product_trx_model->product_name    =   $product_model->name;

                $product_trx_model->total_qty       =   intval($product['product_qty']);

                $data['total_amount']   =   $data['total_amount'] + $product_trx_model->sale_price;
                $data['total_profit']   =   $data['total_profit'] + $product_trx_model->sale_profit;

                $product_trx_models[]   =   $product_trx_model;
            }

            $transaction = TransactionSale::create($data);

            foreach ($product_trx_models as $product_trx_model) {
                $product_trx_model->transaction()->associate($transaction);

                $product_trx_model->save();
            }

            DB::commit();

            Notification::make()
                ->title('Berhasil Menyimpan Data')
                ->success()
                ->send();

            return $transaction;
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
