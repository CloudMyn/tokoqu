<?php

namespace App\Filament\Resources\StoreDashboard\TransactionBuyResource\Pages;

use App\Filament\Resources\StoreDashboard\TransactionBuyResource;
use App\Models\Store;
use App\Models\SupplierProduct;
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

    protected static ?string $title  =   "Input Transaksi Pembelian";

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function handleRecordCreation(array $data): Model
    {
        try {
            DB::beginTransaction();

            $user = get_auth_user();

            $store  =   get_context_store();

            $products       =   $data['products'];

            unset($data['products']);

            $supplier   =   $store->suppliers()->findOrFail($data['supplier_id']);

            $data['store_code']     =   $store->code;
            $data['admin_name']     =   $user->name;
            $data['admin_id']       =   $user->id;
            $data['supplier_id']    =   $supplier->id;
            $data['total_qty'] = array_reduce($products, function ($acc, $item) {
                return $acc + intval($item['product_qty']);
            }, 0);

            $transaction = TransactionBuy::create($data);

            // create assign supplier products


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
                ]);

                // assign supplier product

                $product_supplier_model  =   SupplierProduct::firstOrNew([
                    'product_id'    =>  $product_model->id,
                    'supplier_id'   =>  $supplier->id,
                ]);

                $product_supplier_model->name   =   $product_model->name;
                $product_supplier_model->price  =   $product_cost;

                $product_supplier_model->product()->associate($product_model);
                $product_supplier_model->supplier()->associate($supplier);

                $product_supplier_model->save();
            }

            $new_assets = $store->assets - $data['total_cost'];

            if ($new_assets < 0) {
                throw new \Exception('Kas toko tidak mencukupi jumlah transaksi total kas toko Rp. '
                    . ubah_angka_int_ke_rupiah($store->assets) . ' dan total transaksi saat ini Rp. '
                    . ubah_angka_int_ke_rupiah($data['total_cost']) . ' tidak mencukupi');
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
                ->title('Gagal Menyimpan Data')
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
}
