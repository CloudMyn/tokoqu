<?php

namespace App\Filament\Resources\OwnerStoreResource\Pages;

use App\Filament\Resources\OwnerStoreResource;
use App\Models\Owner;
use App\Models\PhoneNumber;
use App\Models\User;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notification as NotificationsNotification;
use Illuminate\Support\Facades\DB;

class CreateOwnerStore extends CreateRecord
{
    protected static string $resource = OwnerStoreResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        try {
            DB::beginTransaction();

            $phone_number_d =   $data['phone_number'];
            $owner_data     =   $data['owner_store'];

            $phone_number   =   new PhoneNumber();

            $phone_number->phone_number =   $phone_number_d['phone_number'];
            $phone_number->phone_code   =   $phone_number_d['phone_code'];
            $phone_number->phone_verified_at    =   now();

            $phone_number->save();

            $user       =   new User();

            $user->phone_number()->associate($phone_number);

            $user->name     =   $data['name'];
            $user->email    =   $data['email'];
            $user->password =   bcrypt($data['password']);
            $user->role     =   'store_owner';
            $user->email_verified_at    =   now();

            $user->save();

            $owner      =   new Owner();

            $owner->user()->associate($user);

            $owner->name    =   $owner_data['name'];
            $owner->code    =   $owner_data['code'];
            $owner->level   =   $owner_data['level'];

            $owner->save();

            Notification::make()
                ->title('Berhasil Menyimpan Data')
                ->success()
                ->send();

            DB::commit();

            return $user;
        } catch (\Throwable $th) {

            DB::rollBack();

            throw $th;
        }
    }


    protected function getCreatedNotification(): ?Notification
    {
        return null;
    }
}
