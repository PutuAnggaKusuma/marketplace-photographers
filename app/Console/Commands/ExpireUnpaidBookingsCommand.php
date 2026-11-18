<?php

namespace App\Console\Commands;

use App\Services\BookingExpirationService;
use Illuminate\Console\Command;

class ExpireUnpaidBookingsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:expire-unpaid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membatalkan reservasi yang melewati batas waktu pembayaran atau tanggal pemotretan';

    /**
     * Execute the console command.
     */
    public function handle(BookingExpirationService $expirationService): int
    {
        $this->info('Memulai pengecekan pesanan kadaluarsa...');
        $count = $expirationService->expireOutdatedBookings();

        if ($count > 0) {
            $this->info("Berhasil membatalkan {$count} pesanan yang kadaluarsa.");
        } else {
            $this->info('Tidak ada pesanan kadaluarsa yang ditemukan.');
        }

        return self::SUCCESS;
    }
}
