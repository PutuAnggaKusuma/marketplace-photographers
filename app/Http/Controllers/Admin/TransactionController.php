<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Tampilkan Halaman Monitoring Transaksi & Escrow Admin.
     */
        public function index(Request $request)
    {
        $statusFilter = $request->query('status', 'all');
        $sort = $request->query('sort', 'latest');
        $search = $request->query('search', '');

        $query = Contract::with([
            'client.user',
            'photographer.user',
            'bookingDetail.service',
            'payments',
            'cancellation'
        ]);

        if ($statusFilter !== 'all') {
            $query->where('status_payout', $statusFilter);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('client', function ($cq) use ($search) {
                    $cq->where('nama', 'like', "%{$search}%");
                })->orWhereHas('photographer', function ($pq) use ($search) {
                    $pq->where('nama', 'like', "%{$search}%");
                })->orWhereHas('payments', function ($payq) use ($search) {
                    $payq->where('external_id', 'like', "%{$search}%");
                });
            });
        }

        if ($sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } elseif ($sort === 'highest') {
            $query->orderBy('jumlah', 'desc');
        } elseif ($sort === 'lowest') {
            $query->orderBy('jumlah', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $perPage = (int) $request->query('per_page', 10);
        if (!in_array($perPage, [10, 20, 50, 100])) $perPage = 10;
        $contracts = $query->paginate($perPage)->withQueryString();

        // Calculate Overview Statistics
        $allContracts = Contract::all();
        $allElearning = \App\Models\ElearningTransaction::all();
        
        $totalElearningRevenue = $allElearning->where('payment_status', 'completed')->sum('nominal');
        $totalElearningSales = $allElearning->where('payment_status', 'completed')->count();

        // Query Contest Registrations with Fees
        $contestRegistrations = \App\Models\ContestRegistration::with(['contest', 'user'])
            ->whereHas('contest', function($q) {
                $q->where('biaya_pendaftaran', '>', 0);
            })
            ->latest()
            ->get();

        $allPaidContests = $contestRegistrations->where('status_pembayaran', 'lunas');
        $totalContestRevenue = $allPaidContests->sum(fn($r) => (float) ($r->contest->biaya_pendaftaran ?? 0));
        $totalContestSales = $allPaidContests->count();

        $totalGrossVolume = $allContracts->where('status_payout', '!=', 'refunded')->sum('jumlah') + $totalElearningRevenue + $totalContestRevenue;
        $totalEscrowHolding = $allContracts->where('status_payout', 'holding')->sum('payout_amount');
        $totalEscrowReady = $allContracts->where('status_payout', 'ready')->sum('payout_amount');
        $totalPayoutPaid = $allContracts->where('status_payout', 'paid')->sum('payout_amount');
        $totalMarketplaceFee = $allContracts->where('status_payout', '!=', 'refunded')->sum('fee_marketplace');
        $totalPlatformRevenue = $totalMarketplaceFee + $totalElearningRevenue + $totalContestRevenue;

        // Query Elearning Transactions
        $elearningTransactions = \App\Models\ElearningTransaction::with(['user', 'elearning'])
            ->latest()
            ->get();

        return view('admin.transactions.index', compact(
            'contracts',
            'elearningTransactions',
            'contestRegistrations',
            'statusFilter',
            'sort',
            'search',
            'totalGrossVolume',
            'totalEscrowHolding',
            'totalEscrowReady',
            'totalPayoutPaid',
            'totalMarketplaceFee',
            'totalElearningRevenue',
            'totalElearningSales',
            'totalContestRevenue',
            'totalContestSales',
            'totalPlatformRevenue'
        ));
    }

    /**
     * Tampilkan Detail Transaksi Escrow (JSON untuk Modal atau View).
     */
    public function show(Request $request, $id)
    {
        $contract = Contract::with([
            'client.user',
            'photographer.user',
            'bookingDetail.service',
            'bookingDetail.province',
            'bookingDetail.city',
            'payments',
            'testimonial',
            'cancellation'
        ])->findOrFail($id);

        $feeAmount = $contract->fee_marketplace > 0 ? $contract->fee_marketplace : ($contract->jumlah * 0.10);
        $netPayout = $contract->payout_amount > 0 ? $contract->payout_amount : ($contract->jumlah - $feeAmount);

        $data = [
            'id' => $contract->id,
            'contract_code' => '#KTRK-' . str_pad($contract->id, 5, '0', STR_PAD_LEFT),
            'created_at' => $contract->created_at->format('d M Y, H:i'),
            'status_contract' => $contract->status_contract,
            'status_payout' => $contract->status_payout ?? 'holding',
            'payout_at' => $contract->payout_at ? $contract->payout_at->format('d M Y, H:i') : null,
            'payout_requested_at' => $contract->payout_requested_at ? $contract->payout_requested_at->format('d M Y, H:i') : null,
            'payout_notes' => $contract->payout_notes,
            'jumlah' => (float) $contract->jumlah,
            'jumlah_formatted' => 'Rp ' . number_format($contract->jumlah, 0, ',', '.'),
            'fee_amount' => (float) $feeAmount,
            'fee_amount_formatted' => 'Rp ' . number_format($feeAmount, 0, ',', '.'),
            'net_payout' => (float) $netPayout,
            'net_payout_formatted' => 'Rp ' . number_format($netPayout, 0, ',', '.'),
            'client_name' => $contract->client->nama ?? 'Klien',
            'client_email' => $contract->client->user->email ?? '-',
            'client_phone' => $contract->client->nomor_telepon ?? '-',
            'photographer_name' => $contract->photographer->nama ?? 'Fotografer',
            'photographer_email' => $contract->photographer->user->email ?? '-',
            'photographer_phone' => $contract->photographer->nomor_telepon ?? '-',
            'photographer_bank_name' => $contract->photographer->nama_bank ?? null,
            'photographer_bank_account' => $contract->photographer->nomor_rekening ?? null,
            'photographer_bank_holder' => $contract->photographer->atas_nama_rekening ?? null,
            'service_name' => $contract->bookingDetail->service->nama_layanan ?? 'Paket Foto',
            'booking_date' => $contract->bookingDetail && $contract->bookingDetail->booking_date ? date('d M Y', strtotime($contract->bookingDetail->booking_date)) : '-',
            'booking_time' => $contract->bookingDetail && $contract->bookingDetail->jam_mulai ? ($contract->bookingDetail->jam_mulai . ($contract->bookingDetail->jam_selesai ? ' - ' . $contract->bookingDetail->jam_selesai : '') . ' WIB') : '-',
            'location' => $contract->bookingDetail->lokasi ?? '-',
            'status_booking' => $contract->bookingDetail->status_booking ?? 'pending',
            'hasil_foto_url' => $contract->bookingDetail->hasil_foto_url ?? null,
            'catatan_fotografer' => $contract->bookingDetail->catatan_fotografer ?? null,
            'payment_status' => $contract->payments->first()->payment_status ?? 'pending',
            'payment_type' => $contract->payments->first()->payment_type ?? 'transfer',
            'payment_external_id' => $contract->payments->first()->external_id ?? 'INV-XXXXX',
            'bukti_transfer' => $contract->payments->first()->bukti_transfer ?? null,
            'cancellation_reason' => $contract->cancellation->alasan ?? null,
            'cancelled_by' => $contract->cancellation->dibatalkan_oleh ?? null,
            'refund_amount' => $contract->cancellation ? 'Rp ' . number_format($contract->cancellation->refund_amount, 0, ',', '.') : null,
        ];

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $data]);
        }

        return redirect()->route('admin.transactions', ['search' => '#KTRK-' . str_pad($contract->id, 5, '0', STR_PAD_LEFT)]);
    }

    /**
     * Verifikasi Pembayaran Transfer Klien oleh Admin.
     */
    public function verifyPayment(Request $request, $id)
    {
        $contract = Contract::with(['client.user', 'photographer.user', 'bookingDetail', 'payments'])->findOrFail($id);

        if ($contract->status_contract === 'cancelled') {
            return redirect()->back()->with('error', 'Pesanan sesi foto yang telah dibatalkan tidak dapat diverifikasi pembayarannya.');
        }

        $existingPayment = $contract->payments->where('payment_status', 'completed')->first();
        if ($contract->status_contract === 'active' && $existingPayment) {
            return redirect()->back()->with('error', 'Pembayaran sesi ini sudah diverifikasi sebelumnya.');
        }

        $payment = $contract->payments->where('payment_status', 'pending')->first() ?: $contract->payments->first();
        if ($payment) {
            $payment->update([
                'payment_status' => 'completed',
            ]);
        }

        $contract->update([
            'status_contract' => 'active',
            'status_payout' => 'holding',
            'is_validated_client' => true,
        ]);

        if ($contract->bookingDetail) {
            $contract->bookingDetail->update([
                'status_booking' => 'confirmed'
            ]);
        }

        $code = '#KTRK-' . str_pad($contract->id, 5, '0', STR_PAD_LEFT);

        // Notifikasi ke Klien
        if ($contract->client && $contract->client->id_user) {
            \App\Models\Notification::send(
                $contract->client->id_user,
                'Pembayaran Sesi Foto Terverifikasi',
                "Pembayaran untuk sesi {$code} telah diverifikasi oleh Admin. Jadwal pemotretan Anda kini aktif!",
                'payment_verified',
                url('/client/bookings')
            );
        }

        // Notifikasi ke Fotografer
        if ($contract->photographer && $contract->photographer->id_user) {
            \App\Models\Notification::send(
                $contract->photographer->id_user,
                'Pesanan Foto Baru Dikonfirmasi',
                "Klien telah melunasi pembayaran sesi {$code} dan telah diverifikasi oleh Admin. Jadwal pemotretan siap dilaksanakan.",
                'booking_confirmed',
                url('/photographer/bookings')
            );
        }

        \App\Models\ActivityLog::record(
            'payment_verified_admin',
            "Admin memverifikasi pembayaran transfer untuk sesi {$code}",
            $contract
        );

        return redirect()->back()->with('success', "Pembayaran sesi {$code} berhasil diverifikasi! Jadwal sesi telah aktif.");
    }

    /**
     * Tolak Bukti Transfer Klien oleh Admin.
     */
    public function rejectPayment(Request $request, $id)
    {
        $contract = Contract::with(['client.user', 'payments'])->findOrFail($id);
        $payment = $contract->payments->where('payment_status', 'pending')->first() ?: $contract->payments->first();

        if ($payment && $payment->payment_status === 'completed') {
            return redirect()->back()->with('error', 'Pembayaran yang sudah berstatus lunas (completed) tidak dapat ditolak.');
        }

        if (in_array($contract->status_contract, ['completed', 'cancelled'])) {
            return redirect()->back()->with('error', 'Status pesanan sesi ini tidak dapat diubah lagi.');
        }

        $alasan = $request->input('alasan', 'Bukti transfer tidak valid atau nominal tidak sesuai.');
        if ($payment) {
            $payment->update([
                'payment_status' => 'pending',
                'bukti_transfer' => null, // reset agar klien dapat mengunggah ulang
            ]);
        }

        $code = '#KTRK-' . str_pad($contract->id, 5, '0', STR_PAD_LEFT);

        if ($contract->client && $contract->client->id_user) {
            \App\Models\Notification::send(
                $contract->client->id_user,
                'Bukti Transfer Ditolak Admin',
                "Bukti transfer untuk sesi {$code} ditolak Admin: {$alasan}. Silakan unggah ulang bukti transfer yang valid.",
                'payment_rejected',
                url('/pembayaran/' . $contract->id)
            );
        }

        \App\Models\ActivityLog::record(
            'payment_rejected_admin',
            "Admin menolak bukti transfer sesi {$code}: {$alasan}",
            $contract
        );

        return redirect()->back()->with('success', "Bukti transfer sesi {$code} berhasil ditolak. Klien diminta untuk mengunggah ulang.");
    }

    /**
     * Cairkan Dana Payout (90%) ke Rekening Fotografer.
     */
    public function releasePayout(Request $request, $id)
    {
        $contract = Contract::with(['photographer.user', 'client.user'])->findOrFail($id);

        if ($contract->status_payout !== 'ready') {
            return redirect()->back()->with('error', 'Dana payout hanya dapat dicairkan jika status escrow telah "ready" (klien telah menyetujui hasil foto).');
        }

        $request->validate([
            'payout_notes' => 'nullable|string|max:500',
            'bukti_transfer_payout' => 'nullable|image|max:10240',
        ]);

        $feeRate = 0.10; // 10%
        $feeMarketplace = $contract->jumlah * $feeRate;
        $payoutAmount = $contract->jumlah - $feeMarketplace;

        $notes = $request->input('payout_notes') ?: 'Pencairan dana payout disetujui Admin.';
        if ($request->hasFile('bukti_transfer_payout')) {
            $path = $request->file('bukti_transfer_payout')->store('payout_proofs', 'public');
            $payoutSlipUrl = asset('storage/' . $path);
            $notes .= " | Bukti Transfer Bank: " . $payoutSlipUrl;
        }

        $contract->update([
            'status_payout' => 'paid',
            'fee_marketplace' => $feeMarketplace,
            'payout_amount' => $payoutAmount,
            'payout_at' => now(),
            'payout_notes' => $notes,
        ]);

        $formattedAmount = 'Rp ' . number_format($payoutAmount, 0, ',', '.');
        $photoName = $contract->photographer->nama ?? 'Fotografer';

        // Dispatch Notification to Photographer
        if ($contract->photographer && $contract->photographer->id_user) {
            \App\Models\Notification::send(
                $contract->photographer->id_user,
                'Payout Dana Berhasil Dicairkan',
                "Dana Payout sebesar {$formattedAmount} untuk sesi #KTRK-" . str_pad($contract->id, 5, '0', STR_PAD_LEFT) . " telah dicairkan ke rekening Anda.",
                'payout',
                url('/photographer/reports')
            );
        }

        // Record Activity Log
        \App\Models\ActivityLog::record(
            'payout_released',
            "Admin mencairkan dana payout sebesar {$formattedAmount} ke fotografer {$photoName} untuk sesi #KTRK-" . str_pad($contract->id, 5, '0', STR_PAD_LEFT),
            $contract,
            ['payout_amount' => $payoutAmount, 'fee_marketplace' => $feeMarketplace]
        );

        return redirect()->back()->with('success', "Payout sebesar {$formattedAmount} berhasil dicairkan ke {$photoName}!");
    }

    /**
     * Process Refund Dana ke Klien.
     */
    public function processRefund(Request $request, $id)
    {
        $contract = Contract::with(['client.user', 'photographer.user', 'bookingDetail', 'cancellation'])->findOrFail($id);

        if ($contract->status_payout === 'paid') {
            return redirect()->back()->with('error', 'Transaksi tidak dapat direfund karena dana payout sudah dicairkan ke fotografer.');
        }

        if ($contract->status_payout === 'refunded') {
            return redirect()->back()->with('error', 'Transaksi ini sudah direfund sebelumnya.');
        }

        $request->validate([
            'refund_notes' => 'nullable|string|max:500'
        ]);

        $notes = $request->input('refund_notes') ?: 'Refund dana disetujui Admin.';

        $contract->update([
            'status_payout' => 'refunded',
            'status_contract' => 'cancelled',
            'fee_marketplace' => 0,
            'payout_amount' => 0,
            'payout_notes' => $notes,
        ]);

        if ($contract->bookingDetail) {
            $contract->bookingDetail->update(['status_booking' => 'cancelled']);
        }

        if (!$contract->cancellation) {
            \App\Models\ContractCancellation::create([
                'id_contract' => $contract->id,
                'alasan' => $notes,
                'dibatalkan_oleh' => 'admin',
                'refund_amount' => $contract->jumlah,
            ]);
        }

        $clientName = $contract->client->nama ?? 'Klien';
        $formattedAmount = 'Rp ' . number_format($contract->jumlah, 0, ',', '.');

        // Kirim Notifikasi in-app ke Klien
        if ($contract->client && $contract->client->id_user) {
            \App\Models\Notification::send(
                $contract->client->id_user,
                'Pengembalian Dana (Refund) Berhasil',
                "Dana sebesar {$formattedAmount} untuk sesi #KTRK-" . str_pad($contract->id, 5, '0', STR_PAD_LEFT) . " telah disetujui untuk dikembalikan ke akun Anda. Catatan: {$notes}",
                'refund',
                url('/client/bookings')
            );
        }

        // Kirim Notifikasi in-app ke Fotografer
        if ($contract->photographer && $contract->photographer->id_user) {
            \App\Models\Notification::send(
                $contract->photographer->id_user,
                'Pembatalan & Refund Sesi Booking',
                "Sesi booking #KTRK-" . str_pad($contract->id, 5, '0', STR_PAD_LEFT) . " telah dibatalkan dan dana direfund ke klien. Catatan: {$notes}",
                'refund',
                url('/photographer/bookings')
            );
        }

        // Record Activity Log
        \App\Models\ActivityLog::record(
            'refund_processed',
            "Admin menyetujui pengembalian dana (refund) sebesar {$formattedAmount} ke klien {$clientName} untuk sesi #KTRK-" . str_pad($contract->id, 5, '0', STR_PAD_LEFT),
            $contract,
            ['refund_amount' => $contract->jumlah, 'notes' => $notes]
        );

        return redirect()->back()->with('success', "Dana transaksi sebesar {$formattedAmount} berhasil direfund ke akun {$clientName}.");
    }
}