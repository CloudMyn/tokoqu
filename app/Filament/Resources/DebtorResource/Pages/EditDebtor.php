<?php

namespace App\Filament\Resources\DebtorResource\Pages;

use App\Filament\Resources\DebtorResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditDebtor extends EditRecord
{
    protected static string $resource = DebtorResource::class;

    protected static ?string $title = 'Edit Data Debitur';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }


    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($data['paid'] != $data['amount'] && $data['status'] == 'paid') {

            Notification::make()
                ->title('Penyelesaian Peminjaman!')
                ->body('Jumlah pembayaran yang diinputkan tidak sama dengan jumlah peminjaman. Silahkan cek kembali.')
                ->danger()
                ->send();

            $this->halt();
        }

        return $data;
    }
}
