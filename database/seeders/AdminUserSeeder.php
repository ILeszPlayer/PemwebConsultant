<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@hexaspace.com'],
            [
                'name' => 'Admin Hexa Space',
                'email' => 'admin@hexaspace.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        $this->command->info('Akun admin berhasil dibuat. Email: admin@hexaspace.com, Password: password');
    }
}
