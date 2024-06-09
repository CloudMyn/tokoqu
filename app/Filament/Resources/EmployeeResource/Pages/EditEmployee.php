<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use App\Models\Store;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EditEmployee extends EditRecord
{
    protected static string $resource = EmployeeResource::class;

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

            $employee_data = $data['employee'];


            $employee = $record->employee;

            $employee->store()->associate(Store::where('code', $employee_data['store_code'])->firstOrFail());

            if ($employee_data['ktp_photo[]']) {
                $employee->ktp_photo    =   $employee_data['ktp_photo[]'];
            }

            $employee->full_name    =   $employee_data['full_name'];
            $employee->ktp_number   =   $employee_data['ktp_number'];
            $employee->start_working_at =   $employee_data['start_working_at'];
            $employee->employee_code    =   $employee_data['employee_code'];

            $employee->save();

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

            return $employee;
        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
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
