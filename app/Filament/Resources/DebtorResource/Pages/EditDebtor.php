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

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

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
        if ($data['status'] == 'paid') {
            $data['paid'] = $data['amount'];
        }

        if ($data['paid'] === $data['amount'] && $data['status'] != 'paid') {

            $data['status'] = 'paid';
        }

        if(intval($data['paid']) > intval($data['amount'])) {

            Notification::make()
                ->title('Peringatan')
                ->body('Jumlah yang dibayarkan melebihi nominal peminjaman')
                ->danger()
                ->send();

            $this->halt();
        }


        return $data;
    }
}
