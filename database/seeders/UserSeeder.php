<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@kopiluwak.id'],
            [
                'nama' => 'Administrator',
                'password' => 'password',
                'no_hp' => '081234567890',
                'alamat' => 'Kudus, Jawa Tengah',
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'user@kopiluwak.id'],
            [
                'nama' => 'User Demo',
                'password' => 'password',
                'no_hp' => '081298765432',
                'alamat' => 'Kudus, Jawa Tengah',
                'role' => 'user',
            ]
        );
    }
}
