<?php

namespace Database\Seeders;

use App\Models\RoleAdmin;
use App\Models\RoleClient;
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
        $superAdmin = User::updateOrCreate(
            ['email' => 'superadmin@lensmatch.com'],
            [
                'nama' => 'Super Admin LensMatch',
                'password' => Hash::make('password123'),
                'role' => 'super_admin',
                'is_protected' => true,
                'email_verified_at' => now(),
            ]
        );
        RoleAdmin::updateOrCreate(
            ['id_user' => $superAdmin->id],
            [
                'nama' => 'Super Admin LensMatch',
                'no_telepon' => '081200000001',
            ]
        );

        // 2. Staff Admin 1
        $admin1 = User::updateOrCreate(
            ['email' => 'admin@lensmatch.com'],
            [
                'nama' => 'Admin 1',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'is_protected' => false,
                'email_verified_at' => now(),
            ]
        );
        RoleAdmin::updateOrCreate(
            ['id_user' => $admin1->id],
            [
                'nama' => 'Admin 1',
                'no_telepon' => '081200000002',
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

        // 5. Clients (6 Akun Klien Terpisah agar Data Kontrak, Invoice, & Ulasan Terisolasi)
        $clients = [
            [
                'email' => 'dian@gmail.com',
                'nama' => 'Dian & Febri',
                'nomor_telepon' => '081234567899',
                'alamat' => 'Jakarta Selatan',
            ],
            [
                'email' => 'mahkota@event.com',
                'nama' => 'PT Mahkota Event Organizer',
                'nomor_telepon' => '081999888777',
                'alamat' => 'Semarang',
            ],
            [
                'email' => 'bulilik@kuliner.com',
                'nama' => 'Bu Lilik Kuliner Nusantara',
                'nomor_telepon' => '081567891234',
                'alamat' => 'Bandung',
            ],
            [
                'email' => 'reza.itb@gmail.com',
                'nama' => 'Reza Pratama',
                'nomor_telepon' => '081387654321',
                'alamat' => 'Yogyakarta',
            ],
            [
                'email' => 'siti.rahma@gmail.com',
                'nama' => 'Siti Rahmawati',
                'nomor_telepon' => '085712345678',
                'alamat' => 'Surabaya',
            ],
            [
                'email' => 'finance@baliluxury.com',
                'nama' => 'PT Bali Luxury Resort',
                'nomor_telepon' => '08113987654',
                'alamat' => 'Seminyak, Bali',
            ],
        ];

        foreach ($clients as $c) {
            $clientUser = User::updateOrCreate(
                ['email' => $c['email']],
                [
                    'nama' => $c['nama'],
                    'password' => Hash::make('password123'),
                    'role' => 'client',
                    'is_protected' => false,
                    'email_verified_at' => now(),
                ]
            );
            RoleClient::updateOrCreate(
                ['id_user' => $clientUser->id],
                [
                    'nama' => $c['nama'],
                    'nomor_telepon' => $c['nomor_telepon'],
                    'alamat' => $c['alamat'],
                ]
            );
        }
    }
}
