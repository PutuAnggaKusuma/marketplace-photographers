<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Contract;
use App\Models\RolePhotographer;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActivityLogSeeder extends Seeder
{
    /**
     * Seed initial realistic activity logs.
     */
    public function run(): void
    {
        $superAdmin = User::where('role', 'super_admin')->first() ?: User::first();
        $photographer = RolePhotographer::first();
        $contract = Contract::first();

        $entries = [
            [
                'id_user' => $superAdmin?->id,
                'log_name' => 'payout_released',
                'description' => 'Admin mencairkan dana payout sebesar Rp 1.800.000 ke studio ' . ($photographer?->nama ?? 'Aura Lens Photography') . ' untuk sesi KTRK-00001',
                'subject_type' => Contract::class,
                'subject_id' => $contract?->id ?? 1,
                'properties' => ['payout_amount' => 1800000, 'fee_marketplace' => 200000],
                'created_at' => now()->subHours(2),
            ],
            [
                'id_user' => $superAdmin?->id,
                'log_name' => 'photographer_verified',
                'description' => "Admin mengubah status verifikasi studio '" . ($photographer?->nama ?? 'Aura Lens Photography') . "' menjadi: diverifikasi (Centang Biru Aktif)",
                'subject_type' => RolePhotographer::class,
                'subject_id' => $photographer?->id ?? 1,
                'properties' => ['is_verified' => true],
                'created_at' => now()->subHours(5),
            ],
            [
                'id_user' => $photographer?->id_user ?? $superAdmin?->id,
                'log_name' => 'gallery_uploaded',
                'description' => 'Fotografer ' . ($photographer?->nama ?? 'Aura Lens') . ' mengunggah tautan galeri foto untuk sesi KTRK-00001',
                'subject_type' => Contract::class,
                'subject_id' => $contract?->id ?? 1,
                'properties' => ['hasil_foto_url' => 'https://drive.google.com/drive/folders/demo-lensmatch'],
                'created_at' => now()->subHours(12),
            ],
            [
                'id_user' => $photographer?->id_user ?? $superAdmin?->id,
                'log_name' => 'booking_confirmed',
                'description' => 'Fotografer ' . ($photographer?->nama ?? 'Aura Lens') . ' mengonfirmasi booking sesi KTRK-00001',
                'subject_type' => Contract::class,
                'subject_id' => $contract?->id ?? 1,
                'properties' => null,
                'created_at' => now()->subDays(1),
            ],
            [
                'id_user' => $contract?->client?->id_user ?? $superAdmin?->id,
                'log_name' => 'payment_confirmed',
                'description' => 'Pembayaran sebesar Rp 2.000.000 untuk sesi KTRK-00001 telah diverifikasi dan masuk ke Rekening Bersama Escrow',
                'subject_type' => Contract::class,
                'subject_id' => $contract?->id ?? 1,
                'properties' => ['amount' => 2000000, 'status_payout' => 'holding'],
                'created_at' => now()->subDays(2),
            ],
            [
                'id_user' => $contract?->client?->id_user ?? $superAdmin?->id,
                'log_name' => 'booking_created',
                'description' => "Klien membuat reservasi sesi KTRK-00001 untuk layanan 'Paket Prewedding Gold'",
                'subject_type' => Contract::class,
                'subject_id' => $contract?->id ?? 1,
                'properties' => ['total_amount' => 2000000],
                'created_at' => now()->subDays(3),
            ],
        ];

        foreach ($entries as $entry) {
            ActivityLog::create($entry);
        }
    }
}
