<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        User::create([
            'role' => 1,
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin1234'),
            'number_phone' => '087757453838'
        ]);

        User::create([
            'role' => 2,
            'name' => 'Daffa Azhar',
            'email' => 'daffaazhar@gmail.com',
            'password' => Hash::make('daffa1234'),
            'number_phone' => '081276753810'
        ]);

        User::create([
            'role' => 2,
            'name' => 'Fasihul Ilmi',
            'email' => 'ilmifshl@gmail.com',
            'password' => Hash::make('fasihul1234'),
            'number_phone' => '089876579887'
        ]);

        User::create([
            'role' => 2,
            'name' => 'Hafiza Rizky Irland',
            'email' => 'hafiza@gmail.com',
            'password' => Hash::make('hafiza1234'),
            'number_phone' => '087757453838'
        ]);

        User::create([
            'role' => 2,
            'name' => 'Rakha Putra Pratam',
            'email' => 'rakha@gmail.com',
            'password' => Hash::make('rakha1234'),
            'number_phone' => '087765449823'
        ]);
    }
}
