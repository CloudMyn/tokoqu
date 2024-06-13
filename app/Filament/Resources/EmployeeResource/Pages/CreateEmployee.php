<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use App\Models\Employee;
use App\Models\PhoneNumber;
use App\Models\Store;
use App\Models\User;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateEmployee extends CreateRecord
{
    protected static string $resource = EmployeeResource::class;

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

            $phone_number_d =   $data['phone_number'];

            $employee_data = $data['employee'];

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
            $user->role     =   'store_employee';
            $user->email_verified_at    =   now();

            $user->save();

            $employee = new Employee();

            $employee->user()->associate($user);
            $employee->store()->associate(Store::where('code', $employee_data['store_code'])->firstOrFail());
            $employee->owner()->associate(get_auth_user()->owner_store);

            $employee->ktp_photo    =   $employee_data['ktp_photo[]'];
            $employee->full_name    =   $employee_data['full_name'];
            $employee->ktp_number   =   $employee_data['ktp_number'];
            $employee->start_working_at =   $employee_data['start_working_at'];
            $employee->employee_code    =   $employee_data['employee_code'];

            $employee->save();

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

    protected function uploadPhotoKTP(array $data): void
    {
    }

    protected function getCreatedNotification(): ?Notification
    {
        return null;
    }
}
