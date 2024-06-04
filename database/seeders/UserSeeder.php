<?php

namespace Database\Seeders;

use App\Models\Owner;
use App\Models\PhoneNumber;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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

                Store::create($data['store']);
            }
        }
    }
}
