<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserInfo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $devaccess = [
            1 => [
                'username' => 'sdivina',
                'email' => 'sariodivinaroma@gmail.com',
                'password' => Hash::make('admin00'),
                'lastname' => strtoupper('sario'),
                'firstname' => strtoupper('divina roma'),
                'middlename' => strtoupper('de vera'),
                'birthday' => date('1992-06-05'),
                'address1' => strtoupper('zone 4'),
                'region' => strtoupper('region iii'),
                'province' => strtoupper('nueva ecija'),
                'municipality' => strtoupper('lupao'),
                'barangay' => strtoupper('burgos'),
            ],
            2 => [
                'username' => 'ajhay',
                'email' => 'randy.a257@gmail.com',
                'password' => Hash::make('admin01'),
                'lastname' => strtoupper('alabab'),
                'firstname' => strtoupper('jhay'),
                'middlename' => strtoupper('lamarca'),
                'birthday' => date('1992-01-25'),
                'address1' => strtoupper('zone 1'),
                'region' => strtoupper('region iii'),
                'province' => strtoupper('nueva ecija'),
                'municipality' => strtoupper('san jose city'),
                'barangay' => strtoupper('pinili'),
            ],
            3 => [
                'username' => 'pmichael',
                'email' => 'fishykhel@gmail.com',
                'password' => Hash::make('admin02'),
                'lastname' => strtoupper('pascual'),
                'firstname' => strtoupper('michael angelo'),
                'middlename' => strtoupper('cajandab'),
                'birthday' => date('1989-06-26'),
                'address1' => strtoupper('zone 2'),
                'region' => strtoupper('region iii'),
                'province' => strtoupper('nueva ecija'),
                'municipality' => strtoupper('san jose city'),
                'barangay' => strtoupper('abar 1st'),
            ],
            4 => [
                'username' => 'cjonathan',
                'email' => 'jonathancubol4@gmail.com',
                'password' => Hash::make('admin03'),
                'lastname' => strtoupper('cubol'),
                'firstname' => strtoupper('jonathan'),
                'middlename' => strtoupper('sagun'),
                'birthday' => date('2001-08-18'),
                'address1' => strtoupper('zone 3'),
                'region' => strtoupper('region iii'),
                'province' => strtoupper('nueva ecija'),
                'municipality' => strtoupper('lupao'),
                'barangay' => strtoupper('bagong flores'),
            ],
            5 => [
                'username' => 'uednalyn',
                'email' => 'ubaldoednalyn5409@gmail.com',
                'password' => Hash::make('admin04'),
                'lastname' => strtoupper('ubaldo'),
                'firstname' => strtoupper('ednalyn'),
                'middlename' => strtoupper('somera'),
                'birthday' => date('2001-07-19'),
                'address1' => strtoupper('zone 1'),
                'region' => strtoupper('region iii'),
                'province' => strtoupper('nueva ecija'),
                'municipality' => strtoupper('llanera'),
                'barangay' => strtoupper('caridad sur'),
            ]
        ];

        foreach ($devaccess as $user) {
            $user_id = random_int(10000, 99999);
            User::create([
                'user_id' => $user_id,
                'username' => $user['username'],
                'email' => $user['email'],
                'password' => $user['password'],
                'account_status' => 'ACTIVE',
            ])->assignRole('admin');

            UserInfo::create([
                'user_id' => $user_id,
                'lastname' => $user['lastname'],
                'firstname' => $user['firstname'],
                'middlename' => $user['middlename'],
                'birthday' => $user['birthday'],
                'address1' => $user['address1'],
                'region' => $user['region'],
                'province' => $user['province'],
                'municipality' => $user['municipality'],
                'barangay' => $user['barangay'],
            ]);
        }
    }
}
