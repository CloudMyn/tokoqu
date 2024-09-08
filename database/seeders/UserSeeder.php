<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Owner;
use App\Models\PhoneNumber;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user_data = [
            [
                'user'      =>  [
                    'name'      =>  'admin',
                    'email'     =>  'admin@mail.io',
                    'password'  =>  bcrypt('admin@password'),
                    'role'      =>  'admin',
                    'email_verified_at' =>  now(),
                ],

                'phone'     =>  [
                    'phone_number'  =>  '082191615474',
                    'phone_code'    =>  '+62',
                    'phone_verify_code' => generate_phone_verify_code(),
                    'phone_verified_at' =>  now(),
                ],
            ],

            // store 1
            [
                'user'      =>  [
                    'name'      =>  'store',
                    'email'     =>  'store@mail.io',
                    'password'  =>  bcrypt('store@password'),
                    'role'      =>  'store_owner',

                    'email_verified_at' =>  now(),
                ],

                'phone'     =>  [
                    'phone_number'  =>  '082191615471',
                    'phone_code'    =>  '+62',
                    'phone_verify_code' => generate_phone_verify_code(),
                    'phone_verified_at' =>  now(),
                ],

                'owner'     =>  [
                    'name'  =>  'Store Owner Name',
                    'code'  =>  generate_code("OWN"),
                    'level' =>  'free',
                ],

                'store'     =>  [
                    'name'  =>  'Finetion Jaya',
                    'code'  =>  generate_store_code(),
                    'address'   =>  fake()->address,
                    'employee'  =>  [
                        'name'      =>  'employee',
                        'email'     =>  'employee@mail.io',
                        'password'  =>  bcrypt('employee@password'),
                        'role'      =>  'store_employee',
                        'email_verified_at' =>  now(),
                        'data'      =>  [
                            'employee_code'     =>  'DC9910',
                            'full_name'         =>  'Hanabi Tanchou',
                            'ktp_number'        =>  '7371099109189819',
                            'ktp_photo'         =>  'default-user.jpg',
                            'start_working_at'  =>  now()->addDay(-10),
                        ],
                    ],
                ],
            ],


            // store 2
            [
                'user'      =>  [
                    'name'      =>  'store_fim',
                    'email'     =>  'store_fim@mail.io',
                    'password'  =>  bcrypt('store_fim@password'),
                    'role'      =>  'store_owner',

                    'email_verified_at' =>  now(),
                ],

                'phone'     =>  [
                    'phone_number'  =>  '082191615470',
                    'phone_code'    =>  '+62',
                    'phone_verify_code' => generate_phone_verify_code(),
                    'phone_verified_at' =>  now(),
                ],

                'owner'     =>  [
                    'name'  =>  'Fim Store Kro',
                    'code'  =>  generate_code("OWX"),
                    'level' =>  'premium',
                ],

                'store'     =>  [
                    'name'  =>  'Cleuon Corp',
                    'code'  =>  generate_store_code(),
                    'address'   =>  fake()->address,
                    'employee'  =>  [
                        'name'      =>  'employee_fim',
                        'email'     =>  'employee_fim@mail.io',
                        'password'  =>  bcrypt('employee_fim@password'),
                        'role'      =>  'store_employee',
                        'email_verified_at' =>  now(),
                        'data'      =>  [
                            'employee_code'     =>  'DX1940',
                            'full_name'         =>  'Hayabusa Tanchou',
                            'ktp_number'        =>  '7371099109628313',
                            'ktp_photo'         =>  'default-user.jpg',
                            'start_working_at'  =>  now()->addDay(-3),
                        ],
                    ],
                ],
            ],
        ];


        foreach ($user_data as $data) {

            $phone  =   PhoneNumber::create($data['phone']);

            $data['user']['phone_id']  =   $phone->id;

            $user   =   User::create($data['user']);

            if (array_key_exists('store', $data) && array_key_exists('owner', $data)) {

                $data['owner']['user_id']   =   $user->id;

                $owner  =   Owner::create($data['owner']);

                $data['store']['owner_id']  =   $owner->id;

                $employee = $data['store']['employee'];

                unset($data['store']['employee']);

                $store = Store::create($data['store']);

                // Product::factory(5)->create(['store_code' => $store->code]);

                $employee_data =  $employee['data'];

                unset($employee['data']);

                $_emp_number = PhoneNumber::create([
                    'phone_number'  =>  '082' . rand(100000000, 999999999),
                    'phone_code'    =>  '+62',
                    'phone_verify_code' => generate_phone_verify_code(),
                    'phone_verified_at' =>  now(),
                ]);

                $employee['phone_id']   =   $_emp_number->id;

                $_user_employee = User::create($employee);

                $employee_data['user_id']       =   $_user_employee->id;
                $employee_data['store_code']    =   $store->code;
                $employee_data['owner_code']    =   $owner->code;

                Employee::create($employee_data);
            }
        }
    }
}
