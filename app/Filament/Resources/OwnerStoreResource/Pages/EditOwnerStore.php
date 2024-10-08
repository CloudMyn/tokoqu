<?php

namespace App\Filament\Resources\OwnerStoreResource\Pages;

use App\Filament\Resources\OwnerStoreResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;

class EditOwnerStore extends EditRecord
{
    protected static string $resource = OwnerStoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        try {
            DB::beginTransaction();

            // write code

            $phone_number_data = $data['phone_number'];

            $owner_data = $data['owner_store'];


            $owner = $record->owner_store;

            $owner->name = $owner_data['name'];
            $owner->code = $owner_data['code'];
            $owner->level = $owner_data['level'];

            $owner->save();

            $phone_number = $record->phone_number;

            $phone_number->phone_number = $phone_number_data['phone_number'];
            $phone_number->phone_code = $phone_number_data['phone_code'];
            $phone_number->phone_verified_at = now();

            $phone_number->save();

            $user = $record;

            $user->name = $data['name'];
            $user->email = $data['email'];

            if (!empty($data['password'])) {
                $user->password = bcrypt($data['password']);
            }

            $user->save();

            DB::commit();

            return $owner;
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    protected function getUpdatedNotification(): ?Notification
    {
        return null;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Check if the password field is not empty
        if (empty($data['password'])) {
            // Remove the password and password_confirmation fields if they are empty
            unset($data['password'], $data['password_confirmation']);
        } else {
            // Encrypt the password if it is set
            $data['password'] = bcrypt($data['password']);
        }

        return $data;
    }
}
