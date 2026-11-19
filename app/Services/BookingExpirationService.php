<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Contract;
use App\Models\ContractCancellation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BookingExpirationService
{
    /**
     * Memeriksa dan membatalkan pesanan yang telah kadaluarsa secara otomatis.
     *
     * Kriteria Kadaluarsa:
     * 1. Status kontrak belum lunas / belum aktif ('draft', 'pending', 'pending_payment', 'approved').
     * 2. DAN memenuhi salah satu kondisi:
     *    a. Jadwal sesi foto telah terlewati (booking_date + jam_mulai <= waktu sekarang).
     *    b. Batas waktu pembayaran (expired_at) telah habis (expired_at <= waktu sekarang).
     *
     * @param int|null $clientId Opsional filter per klien
     * @param int|null $photographerId Opsional filter per fotografer
     * @return int Jumlah kontrak yang dibatalkan
     */
    public function expireOutdatedBookings(?int $clientId = null, ?int $photographerId = null): int
    {
        $now = now();

        $query = Contract::with('bookingDetail')
            ->whereIn('status_contract', ['draft', 'pending', 'pending_payment', 'approved']);

        if ($clientId) {
            $query->where('id_client', $clientId);
        }

        if ($photographerId) {
            $query->where('id_photographer', $photographerId);
        }

        $contracts = $query->get();
        $expiredCount = 0;

        foreach ($contracts as $contract) {
            if ($this->processExpiration($contract, $now)) {
                $expiredCount++;
            }
        }

        return $expiredCount;
    }

    /**
     * Memeriksa dan membatalkan satu kontrak jika terbukti telah kadaluarsa.
     */
    public function checkAndExpireSingle(Contract $contract): bool
    {
        if (in_array($contract->status_contract, ['completed', 'active', 'cancelled'])) {
            return false;
        }

        return $this->processExpiration($contract, now());
    }

    /**
     * Logika inti pembatalan pesanan yang kadaluarsa.
     */
    protected function processExpiration(Contract $contract, Carbon $now): bool
    {
        $booking = $contract->relationLoaded('bookingDetail') ? $contract->bookingDetail : $contract->bookingDetail()->first();
        $isExpired = false;
        $reason = '';

        // 1. Cek jika jadwal sesi pemotretan telah terlewati
        if ($booking && $booking->booking_date) {
            $timeStr = $booking->jam_mulai ? substr($booking->jam_mulai, 0, 5) : '08:00';
            $sessionStart = Carbon::parse($booking->booking_date . ' ' . $timeStr);

            if ($sessionStart->lte($now)) {
                $isExpired = true;
                $reason = 'Jadwal sesi foto telah terlewati tanpa pembayaran';
            }
        }

        // 2. Cek jika batas waktu pembayaran (expired_at) telah berakhir
        if (!$isExpired && $contract->expired_at) {
            $deadline = Carbon::parse($contract->expired_at);
            if ($deadline->lte($now)) {
                $isExpired = true;
                $reason = 'Batas waktu pembayaran telah berakhir';
            }
        }

        if (!$isExpired) {
            return false;
        }

        DB::transaction(function () use ($contract, $booking, $reason, $now) {
            $updateData = ['status_contract' => 'cancelled'];
            if (!$contract->expired_at || Carbon::parse($contract->expired_at)->gt($now)) {
                $updateData['expired_at'] = $now;
            }

            $contract->update($updateData);

            if ($booking) {
                $booking->update([
                    'status_booking' => 'cancelled',
                ]);
            }

            // Batalkan seluruh tagihan yang masih berstatus pending
            $contract->payments()
                ->where('payment_status', 'pending')
                ->update(['payment_status' => 'failed']);

            ContractCancellation::firstOrCreate(
                ['id_contract' => $contract->id],
                [
                    'alasan' => $reason,
                    'dibatalkan_oleh' => null,
                    'refund_amount' => 0,
                ]
            );

            ActivityLog::record(
                'booking_expired',
                "Reservasi KTRK-" . str_pad($contract->id, 5, '0', STR_PAD_LEFT) . " otomatis dibatalkan sistem karena {$reason}",
                $contract
            );
        });

        return true;
    }
}
