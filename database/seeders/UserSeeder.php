<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Super Admin (Permanent Protected Root Account)
        User::updateOrCreate(
            ['email' => 'superadmin@lensmatch.com'],
            [
                'nama' => 'Super Admin LensMatch',
                'password' => Hash::make('password123'),
                'role' => 'super_admin',
                'is_protected' => true,
                'email_verified_at' => now(),
            ]
        );

        // 2. Staff Admin 1
        User::updateOrCreate(
            ['email' => 'admin@lensmatch.com'],
            [
                'nama' => 'Admin 1',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'is_protected' => false,
                'email_verified_at' => now(),
            ]
        );

        // 3. Fotografer (Alex Visual Studio)
        User::updateOrCreate(
            ['email' => 'alex@lensmatch.com'],
            [
                'nama' => 'Alex Visual Studio',
                'password' => Hash::make('password123'),
                'role' => 'photographer',
                'is_protected' => false,
                'email_verified_at' => now(),
            ]
        );

        // 4. Fotografer (Budi Event Captures)
        User::updateOrCreate(
            ['email' => 'budi@lensmatch.com'],
            [
                'nama' => 'Budi Event Captures',
                'password' => Hash::make('password123'),
                'role' => 'photographer',
                'is_protected' => false,
                'email_verified_at' => now(),
            ]
        );

        // 5. Client (Dian & Febri)
        User::updateOrCreate(
            ['email' => 'dian@gmail.com'],
            [
                'nama' => 'Dian & Febri',
                'password' => Hash::make('password123'),
                'role' => 'client',
                'is_protected' => false,
                'email_verified_at' => now(),
            ]
        );
    }
}
