<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'contracts';

    protected $fillable = [
        'id_client',
        'id_photographer',
        'jumlah',
        'payment_type',
        'jumlah_dp',
        'fee_marketplace',
        'payout_amount',
        'status_payout',
        'payout_at',
        'payout_requested_at',
        'payout_notes',
        'is_validated_photographer',
        'is_validated_client',
        'expired_at',
        'status_contract',
    ];

    protected $casts = [
        'payout_at' => 'datetime',
        'payout_requested_at' => 'datetime',
        'expired_at' => 'datetime',
        'fee_marketplace' => 'float',
        'payout_amount' => 'float',
        'jumlah' => 'float',
        'jumlah_dp' => 'float',
    ];

    public function client()
    {
        return $this->belongsTo(RoleClient::class, 'id_client');
    }

    public function photographer()
    {
        return $this->belongsTo(RolePhotographer::class, 'id_photographer');
    }

    public function bookingDetail()
    {
        return $this->hasOne(ContractBooking::class, 'id_contract');
    }

    public function detail()
    {
        return $this->bookingDetail();
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'id_contract');
    }

    public function testimonial()
    {
        return $this->hasOne(Testimonial::class, 'id_contract');
    }

    public function cancellation()
    {
        return $this->hasOne(ContractCancellation::class, 'id_contract');
    }

    public function photographerClientReview()
    {
        return $this->hasOne(PhotographerClientReview::class, 'id_contract');
    }

    /**
     * Standardized Contract Code (Rule #89 Compliant: clean prefix KTRK-, zero hash symbols)
     */
    public function getContractCodeAttribute(): string
    {
        return 'KTRK-' . str_pad($this->id, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Standardized Dynamic Invoice Code (Rule #94 Compliant: derived from payment external_id or dynamic DB attributes)
     */
    public function getInvoiceCodeAttribute(): string
    {
        $payment = $this->relationLoaded('payments') ? $this->payments->first() : $this->payments()->first();
        if ($payment && !empty($payment->external_id) && !str_contains($payment->external_id, 'XXXX')) {
            return $payment->external_id;
        }
        $year = $this->created_at ? $this->created_at->format('Y') : date('Y');
        return 'INV-LM-' . $year . '-' . str_pad($this->id, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Menghitung batas waktu pembayaran adaptif/dinamis (Dynamic Deadline):
     * - Sesi Normal (> 24 jam / H-2 ke atas): batas waktu 24 jam.
     * - Sesi H-1 (12 - 24 jam ke sesi): batas waktu 6 jam (maksimal H-3 jam sebelum sesi).
     * - Sesi Hari Ini / Same-Day (3 - 12 jam ke sesi): batas waktu 1 jam (maksimal H-1.5 jam sebelum sesi).
     * - Sesi Hari Ini Mepet (< 3 jam ke sesi): batas waktu 15 - 30 menit (maksimal H-15 menit sebelum sesi).
     */
    public function calculatePaymentDeadline(?\Carbon\Carbon $fromTime = null): \Carbon\Carbon
    {
        $now = $fromTime ?: now();
        $booking = $this->relationLoaded('bookingDetail') ? $this->bookingDetail : $this->bookingDetail()->first();

        // Fallback jika tidak ada data booking detail
        if (!$booking || !$booking->booking_date) {
            return $now->copy()->addHours(24);
        }

        $timeStr = $booking->jam_mulai ? substr($booking->jam_mulai, 0, 5) : '08:00';
        $sessionStart = \Carbon\Carbon::parse($booking->booking_date . ' ' . $timeStr);

        $minutesUntilSession = (int) $now->diffInMinutes($sessionStart, false);

        // Kasus 1: Sesi sudah lewat atau sudah dimulai saat disetujui
        if ($minutesUntilSession <= 0) {
            return $now->copy()->addMinutes(15);
        }

        // Kasus 2: Sesi sangat mepet (< 3 jam / 180 menit)
        if ($minutesUntilSession < 180) {
            $transferMinutes = min(30, max(15, (int) floor($minutesUntilSession / 3)));
            $deadline = $now->copy()->addMinutes($transferMinutes);
            $maxLimit = $sessionStart->copy()->subMinutes(15);
            return ($deadline->gt($maxLimit) && $maxLimit->gt($now)) ? $maxLimit : $deadline;
        }

        // Kasus 3: Sesi hari ini / Same-Day (3 jam s/d 12 jam / 720 menit)
        if ($minutesUntilSession <= 720) {
            $deadline = $now->copy()->addMinutes(60);
            $maxLimit = $sessionStart->copy()->subMinutes(90);
            return ($deadline->gt($maxLimit) && $maxLimit->gt($now)) ? $maxLimit : $deadline;
        }

        // Kasus 4: Sesi besok / H-1 (12 jam s/d 24 jam / 1440 menit)
        if ($minutesUntilSession <= 1440) {
            $deadline = $now->copy()->addHours(6);
            $maxLimit = $sessionStart->copy()->subHours(3);
            return ($deadline->gt($maxLimit) && $maxLimit->gt($now)) ? $maxLimit : $deadline;
        }

        // Kasus 5: Sesi normal / H-2 ke atas (> 24 jam)
        $deadline = $now->copy()->addHours(24);
        $maxLimit = $sessionStart->copy()->subHours(12);
        return ($deadline->gt($maxLimit) && $maxLimit->gt($now)) ? $maxLimit : $deadline;
    }

    /**
     * Menghasilkan teks deskriptif batas waktu pembayaran untuk notifikasi / copywriting
     */
    public function getPaymentDeadlineDescriptionAttribute(): string
    {
        if (!$this->expired_at) {
            return 'dalam waktu 24 jam';
        }

        $now = now();
        $diffMinutes = (int) $now->diffInMinutes($this->expired_at, false);

        if ($diffMinutes <= 0) {
            return 'segera (waktu pembayaran telah berakhir)';
        }

        if ($diffMinutes <= 60) {
            return "dalam waktu {$diffMinutes} menit (sebelum pukul " . $this->expired_at->translatedFormat('H:i') . ' WIB)';
        }

        if ($diffMinutes <= 720) {
            $hours = (int) ceil($diffMinutes / 60);
            return "dalam waktu {$hours} jam (sebelum pukul " . $this->expired_at->translatedFormat('H:i') . ' WIB)';
        }

        if ($this->expired_at->isToday()) {
            return 'hari ini, sebelum pukul ' . $this->expired_at->translatedFormat('H:i') . ' WIB';
        }

        if ($this->expired_at->isTomorrow()) {
            return 'besok, sebelum pukul ' . $this->expired_at->translatedFormat('H:i') . ' WIB';
        }

        return 'sebelum ' . $this->expired_at->translatedFormat('d F Y, H:i') . ' WIB';
    }
}