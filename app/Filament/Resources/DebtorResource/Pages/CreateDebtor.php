<?php

namespace App\Filament\Resources\DebtorResource\Pages;

use App\Filament\Resources\DebtorResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateDebtor extends CreateRecord
{
    protected static string $resource = DebtorResource::class;

    protected static ?string $title = 'Input Data Debitur';

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $store  =   get_context_store();

        if ($store->assets < intval($data['amount'])) {

            Notification::make()
                ->title('Kas toko tidak cukup!')
                ->body('Kas toko (Rp. ' . ubah_angka_int_ke_rupiah($store->assets) . ')  tidak mencukupi untuk melakukan peminjaman')
                ->danger()
                ->send();

            $this->halt();
        }

        $data['paid']   =   0;
        $data['status'] =   'unpaid';

        return $data;
    }
}
