<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DoctorUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'dr.hendra@hexaspace.com'],
            [
                'name' => 'Hendra',
                'email' => 'dr.hendra@hexaspace.com',
                'password' => Hash::make('password'),
                'role' => 'doctor',
            ]
        );

        $this->command->info('Akun dokter berhasil dibuat. Email: dr.hendra@hexaspace.com, Password: password');
    }
}
