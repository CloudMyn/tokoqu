<?php

namespace App\Filament\Resources\StoreDashboard\TransactionSaleResource\Pages;

use App\Filament\Resources\StoreDashboard\TransactionSaleResource;
use App\Models\Debtor;
use App\Models\TransactionSale;
use App\Models\TransactionSaleItem;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateTransactionSale extends CreateRecord
{
    protected static string $resource = TransactionSaleResource::class;

    protected static ?string $buttonCreateLabel = null;

    protected static bool $canCreateAnother = false;

    protected static ?string $title  =   "Input Transaksi Penjualan";

    protected function handleRecordCreation(array $data): Model
    {
        try {
            DB::beginTransaction();

            $user = get_auth_user();

            $store  =   get_context_store();

            $products       =   $data['products'];
            $trx_discount   =   doubleval($data['discount']) / count($products);

            $in_debt        =    $data['is_debt'];

            $is_deliver     =    $data['is_deliver'];

            $debtor_data    =   $data['debtor_data'];

            unset($data['is_deliver']);
            unset($data['products']);
            unset($data['discount']);
            unset($data['debtor_data']);

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

                $qty    =   intval($product['product_qty']);

                $onkir  =   0;

                $product_model  =    $store->products()->find($product['product_id']);

                if ($is_deliver) {
                    $onkir  =   $product_model->delivery_fee;
                }

                $product_discount   =   doubleval($product['product_discount']);

                $product_trx_model  =   new TransactionSaleItem();

                $product_trx_model->product()->associate($product_model);
                $product_trx_model->store()->associate($store);

                $product_trx_model->sale_product($product_model, $qty, $product_discount + $trx_discount, intval($product['product_discount_per_qty']), $onkir);

                $product_trx_model->product_sku     =   $product_model->sku;
                $product_trx_model->product_name    =   $product_model->name;

                $product_trx_model->total_qty       =   $qty;

                $data['total_amount']   =   $data['total_amount'] + $product_trx_model->sale_price;
                $data['total_profit']   =   $data['total_profit'] + $product_trx_model->sale_profit;

                $product_trx_models[]   =   $product_trx_model;
            }

            if ($data['total_amount'] < 0) {
                throw new \Exception("Transaksi yang anda lakukan tidak dapat diproses, dikarnakan nominal transaksi kurang dari 0");
            }

            if ($data['total_profit'] < 0) {
                throw new \Exception("Transaksi yang anda lakukan tidak dapat diproses, dikarnakan nominal transaksi kurang dari 0");
            }

            $transaction = TransactionSale::create($data);

            foreach ($product_trx_models as $product_trx_model) {
                $product_trx_model->transaction()->associate($transaction);

                $product_trx_model->save();
            }

            $asset = add_store_asset(
                store: $transaction->store,
                title: 'Transaksi Penjualan #' . $transaction->id,
                message: "Transaksi penjualan ID #{$transaction->id} : Rp. " .  ubah_angka_int_ke_rupiah($transaction->total_amount) . " ( " . $transaction->total_qty . " )",
                type: $transaction->is_debt ? 'hold' : 'in',
                amount: $transaction->total_amount,
            );


            if ($in_debt) {

                if ($transaction->total_amount < intval($debtor_data['amount'])) {

                    throw new \Exception("Transaksi yang anda lakukan tidak dapat diproses, dikarnakan nominal pinjaman melebihi total transaksi Rp " . ubah_angka_int_ke_rupiah($transaction->total_amount));
                }

                $debtor_data['paid']            =   0;
                $debtor_data['transaction_id']  =   $transaction->id;
                $debtor_data['asset_id']        =   $asset->id;

                Debtor::create($debtor_data);
            }

            if (
                static::getResource()::isScopedToTenant() &&
                ($tenant = Filament::getTenant())
            ) {
                return $this->associateRecordWithTenant($transaction, $tenant);
            }

            DB::commit();

            Notification::make()
                ->title('Berhasil Menyimpan Data')
                ->body('Anda telah menyimpan data transaksi penjualan')
                ->success()
                ->sendToDatabase($store->owner->user)
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
