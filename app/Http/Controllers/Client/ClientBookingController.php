<?php



namespace App\Http\Controllers\Client;



use App\Http\Controllers\Controller;

use App\Models\Contract;

use App\Models\RoleClient;

use Illuminate\Http\Request;



class ClientBookingController extends Controller

{

    /**

     * Display Client Reservations List.

     */

    public function index(Request $request)

    {

        $clientProfile = RoleClient::where('id_user', auth()->id())->first();



        // If no client profile exists yet, pick first client or create dummy for demo

        if (!$clientProfile) {

            $clientProfile = RoleClient::firstOrCreate(

                ['id_user' => auth()->id()],

                ['nama' => auth()->user()->nama ?? null]

            );

        }



        $query = Contract::with([

            'photographer.user',

            'photographer.province',

            'photographer.city',

            'bookingDetail.service',

            'bookingDetail.province',

            'bookingDetail.city',

            'payments',

            'testimonial',

            'photographerClientReview'

        ])

        ->where('id_client', $clientProfile->id);



        $filterStatus = $request->query('status', 'all');

        if ($filterStatus && $filterStatus !== 'all') {

            if ($filterStatus === 'pending') {

                $query->whereHas('bookingDetail', function ($q) {

                    $q->where('status_booking', 'pending');

                });

            } elseif ($filterStatus === 'confirmed') {

                $query->whereHas('bookingDetail', function ($q) {

                    $q->where('status_booking', 'confirmed');

                });

            } elseif ($filterStatus === 'completed') {

                $query->whereHas('bookingDetail', function ($q) {

                    $q->where('status_booking', 'completed');

                });

            } elseif ($filterStatus === 'cancelled') {

                $query->where(function ($q) {

                    $q->where('status_contract', 'cancelled')

                      ->orWhereHas('bookingDetail', function ($b) {

                          $b->where('status_booking', 'cancelled');

                      });

                });

            }

        }



        $stats = [

            'all' => Contract::where('id_client', $clientProfile->id)->count(),

            'pending' => Contract::where('id_client', $clientProfile->id)

                ->whereHas('bookingDetail', fn($q) => $q->where('status_booking', 'pending'))->count(),

            'confirmed' => Contract::where('id_client', $clientProfile->id)

                ->whereHas('bookingDetail', fn($q) => $q->where('status_booking', 'confirmed'))->count(),

            'completed' => Contract::where('id_client', $clientProfile->id)

                ->whereHas('bookingDetail', fn($q) => $q->where('status_booking', 'completed'))->count(),

        ];



        $contracts = $query->latest()

            ->paginate((int) ($request->query('per_page') && in_array((int)$request->query('per_page'), [10, 20, 50, 100]) ? $request->query('per_page') : 10))

            ->withQueryString();



        // Pengingat Sesi Pintar H-1 & Hari-H untuk Klien

        $urgentSession = \App\Models\ContractBooking::whereHas('contract', function ($q) use ($clientProfile) {

                $q->where('id_client', $clientProfile->id)

                  ->whereNotIn('status_contract', ['cancelled', 'completed']);

            })

            ->upcomingUrgent()

            ->with(['contract.photographer.user', 'service'])

            ->first();



        return view('client.bookings.index', compact('contracts', 'clientProfile', 'filterStatus', 'stats', 'urgentSession'));

    }



    /**

     * Display Client Invoices List.

     */

    public function invoices(Request $request)

    {

        $clientProfile = RoleClient::where('id_user', auth()->id())->first();



        if (!$clientProfile) {

            $clientProfile = RoleClient::firstOrCreate(

                ['id_user' => auth()->id()],

                ['nama' => auth()->user()->nama ?? null]

            );

        }



        $query = Contract::with([

            'photographer.city',

            'bookingDetail.service',

            'payments'

        ])

        ->where('id_client', $clientProfile->id);



        $filterStatus = $request->query('status', 'all');

        if ($filterStatus && $filterStatus !== 'all') {

            if ($filterStatus === 'completed') {

                $query->whereHas('payments', function ($q) {

                    $q->where('payment_status', 'completed');

                });

            } elseif ($filterStatus === 'pending') {

                $query->where(function ($q) {

                    $q->whereDoesntHave('payments')

                      ->orWhereHas('payments', function ($p) {

                          $p->where('payment_status', 'pending');

                      });

                });

            } elseif ($filterStatus === 'failed') {

                $query->whereHas('payments', function ($q) {

                    $q->where('payment_status', 'failed');

                });

            }

        }



        $allContracts = Contract::where('id_client', $clientProfile->id)->with('payments')->get();



        // Query Elearning Transactions for Current User

        $elearningQuery = \App\Models\ElearningTransaction::with('elearning')

            ->where('id_user', auth()->id());



        if ($filterStatus && $filterStatus !== 'all') {

            if ($filterStatus === 'completed') {

                $elearningQuery->where('payment_status', 'completed');

            } elseif ($filterStatus === 'pending') {

                $elearningQuery->where('payment_status', 'pending');

            } elseif ($filterStatus === 'failed') {

                $elearningQuery->where('payment_status', 'failed');

            }

        }



        $elearningTransactions = $elearningQuery->latest()->get();

        $allElearning = \App\Models\ElearningTransaction::where('id_user', auth()->id())->get();



        $elearningPaidCount = $allElearning->where('payment_status', 'completed')->count();

        $elearningPendingCount = $allElearning->where('payment_status', 'pending')->count();

        $elearningPaidAmount = $allElearning->where('payment_status', 'completed')->sum('nominal');



        // Query Paid Contests for Current User

        $contestRegistrations = \App\Models\ContestRegistration::with('contest')

            ->where('id_user', auth()->id())

            ->whereHas('contest', function($q) {

                $q->where('biaya_pendaftaran', '>', 0);

            })

            ->latest()

            ->get();



        $contestPaidCount = $contestRegistrations->where('status_pembayaran', 'lunas')->count();

        $contestPendingCount = $contestRegistrations->whereIn('status_pembayaran', ['menunggu_konfirmasi', 'menunggu_pembayaran'])->count();

        $contestPaidAmount = $contestRegistrations->where('status_pembayaran', 'lunas')->sum(fn($r) => (float) ($r->contest->biaya_pendaftaran ?? 0));



        $contractPaidCount = $allContracts->filter(fn($c) => $c->payments->first() && $c->payments->first()->payment_status === 'completed')->count();

        $contractPendingCount = $allContracts->filter(fn($c) => !$c->payments->first() || $c->payments->first()->payment_status === 'pending')->count();

        $contractPaidAmount = $allContracts->filter(fn($c) => $c->payments->first() && $c->payments->first()->payment_status === 'completed')->sum('jumlah');



        $stats = [

            'all' => $allContracts->count() + $allElearning->count() + $contestRegistrations->count(),

            'completed' => $contractPaidCount + $elearningPaidCount + $contestPaidCount,

            'pending' => $contractPendingCount + $elearningPendingCount + $contestPendingCount,

            'paid_amount' => $contractPaidAmount + $elearningPaidAmount + $contestPaidAmount,

            'contracts_count' => $allContracts->count(),

            'elearning_count' => $allElearning->count(),

            'contest_count' => $contestRegistrations->count(),

        ];



        $contracts = $query->latest()

            ->paginate((int) ($request->query('per_page') && in_array((int)$request->query('per_page'), [10, 20, 50, 100]) ? $request->query('per_page') : 10))

            ->withQueryString();



        return view('client.invoices.index', compact('contracts', 'elearningTransactions', 'contestRegistrations', 'clientProfile', 'filterStatus', 'stats'));

    }



    /**

     * Display Client Photo Galleries (Completed Sessions).

     */

    public function galleries(Request $request)

    {

        $clientProfile = RoleClient::where('id_user', auth()->id())->first();



        if (!$clientProfile) {

            $clientProfile = RoleClient::firstOrCreate(

                ['id_user' => auth()->id()],

                ['nama' => auth()->user()->nama ?? null]

            );

        }



        $contracts = Contract::with([

            'photographer.user',

            'bookingDetail.service',

            'payments',

            'testimonial',

            'photographerClientReview'

        ])

        ->where('id_client', $clientProfile->id)

        ->whereHas('bookingDetail', function ($q) {

            $q->whereNotNull('hasil_foto_url');

        })

        ->latest()

        ->paginate((int) ($request->query('per_page') && in_array((int)$request->query('per_page'), [10, 20, 50, 100]) ? $request->query('per_page') : 10))->withQueryString();



        return view('client.galleries.index', compact('contracts', 'clientProfile'));

    }



    /**

     * Batalkan Reservasi oleh Klien dengan Alasan Pembatalan.

     */

    public function cancelBooking(Request $request, $id)

    {

        $request->validate([

            'alasan' => 'required|string|max:1000',

        ]);



        $clientProfile = RoleClient::where('id_user', auth()->id())->firstOrFail();

        $contract = Contract::with(['photographer.user', 'bookingDetail', 'payments'])

            ->where('id_client', $clientProfile->id)

            ->findOrFail($id);



        if (in_array($contract->status_contract, ['completed', 'cancelled'])) {

            return redirect()->back()->with('error', 'Pesanan yang sudah selesai atau sudah dibatalkan tidak dapat diubah.');

        }



        // Validasi Kebijakan Pembatalan: Maksimal 24 jam sebelum jadwal sesi foto

        if ($contract->bookingDetail && $contract->bookingDetail->booking_date) {

            $timeStr = $contract->bookingDetail->jam_mulai ?? $contract->bookingDetail->booking_time ?? '00:00:00';
            try {
                $sessionTime = \Carbon\Carbon::parse($contract->bookingDetail->booking_date . ' ' . $timeStr);
            } catch (\Exception $e) {}



            if ($sessionTime->isPast() || now()->diffInHours($sessionTime, false) < 24) {

                return redirect()->back()->with('error', 'Pembatalan mandiri hanya dapat diajukan maksimal 24 jam sebelum jadwal sesi pemotretan. Silakan hubungi fotografer atau admin untuk bantuan lebih lanjut.');

            }

        }



        // Cek apakah pembayaran sudah dilakukan (escrow aktif)

        $payment = $contract->payments->where('payment_status', 'completed')->first();

        $isPaid = $payment !== null || in_array($contract->status_payout, ['holding', 'ready']);



        // Update Contract status

        $contractUpdates = ['status_contract' => 'cancelled'];

        if ($isPaid) {

            $contractUpdates['status_payout'] = 'refunded';

            $contractUpdates['payout_notes'] = 'Dibatalkan oleh Klien: ' . $request->alasan;

        }

        $contract->update($contractUpdates);



        // Update Booking detail

        if ($contract->bookingDetail) {

            $contract->bookingDetail->update(['status_booking' => 'cancelled']);

        }



        // Simpan ke tabel contract_cancellations

        \App\Models\ContractCancellation::create([

            'id_contract' => $contract->id,

            'alasan' => $request->alasan,

            'dibatalkan_oleh' => 'client',

            'refund_amount' => $isPaid ? $contract->jumlah : 0,

        ]);



        $code = 'KTRK-' . str_pad($contract->id, 5, '0', STR_PAD_LEFT);



        // Notifikasi ke Fotografer

        if ($contract->photographer && $contract->photographer->id_user) {

            \App\Models\Notification::send(

                $contract->photographer->id_user,

                'Reservasi Dibatalkan oleh Klien',

                "Klien {$clientProfile->nama} telah membatalkan reservasi sesi {$code}. Alasan: {$request->alasan}",

                'booking_cancelled',

                url('/photographer/bookings')

            );

        }



        // Notifikasi ke Klien

        \App\Models\Notification::send(

            auth()->id(),

            'Pembatalan Reservasi Berhasil',

            "Pengajuan pembatalan untuk pesanan {$code} telah diproses." . ($isPaid ? " Pengembalian dana (refund) sebesar Rp " . number_format($contract->jumlah, 0, ',', '.') . " sedang ditinjau oleh Admin." : ""),

            'booking_cancelled',

            url('/client/bookings')

        );



        // Record Activity Log

        \App\Models\ActivityLog::record(

            'booking_cancelled',

            "Klien {$clientProfile->nama} membatalkan reservasi sesi {$code}. Alasan: {$request->alasan}",

            $contract,

            ['alasan' => $request->alasan, 'refund_amount' => $isPaid ? $contract->jumlah : 0]

        );



        return redirect()->back()->with('success', 'Reservasi sesi foto berhasil dibatalkan.');

    }



    /**

     * Konfirmasi Terima Hasil Foto & Selesaikan Pesanan oleh Klien.

     */

    public function confirmGallery(Request $request, $id)

    {

        $clientProfile = RoleClient::where('id_user', auth()->id())->firstOrFail();

        $contract = Contract::with(['photographer.user', 'bookingDetail'])

            ->where('id_client', $clientProfile->id)

            ->findOrFail($id);



        if ($contract->status_contract === 'cancelled') {
            return redirect()->back()->with('error', 'Pesanan sesi foto yang telah dibatalkan tidak dapat dikonfirmasi.');
        }

        if ($contract->status_contract === 'completed' && $contract->is_validated_client) {
            return redirect()->back()->with('info', 'Pesanan sesi foto ini sudah dikonfirmasi selesai sebelumnya.');
        }

        if (!$contract->bookingDetail || empty($contract->bookingDetail->hasil_foto_url)) {

            return redirect()->back()->with('error', 'Fotografer belum mengunggah galeri hasil foto.');

        }



        $contract->update([

            'is_validated_client' => true,

            'status_contract' => 'completed',

            'status_payout' => 'ready',

        ]);



        if ($contract->bookingDetail) {

            $contract->bookingDetail->update(['status_booking' => 'completed']);

        }



        $code = 'KTRK-' . str_pad($contract->id, 5, '0', STR_PAD_LEFT);



        // Notifikasi ke Fotografer

        if ($contract->photographer && $contract->photographer->id_user) {

            \App\Models\Notification::send(

                $contract->photographer->id_user,

                'Hasil Foto Dikonfirmasi Klien',

                "Klien {$clientProfile->nama} telah memeriksa dan mengonfirmasi penerimaan hasil foto sesi {$code}. Dana escrow Anda kini siap dicairkan (ready for payout).",

                'gallery_confirmed',

                url('/photographer/reports')

            );

        }



        // Record Activity Log

        \App\Models\ActivityLog::record(

            'gallery_accepted_by_client',

            "Klien {$clientProfile->nama} menerima & menyelesaikan pesanan sesi {$code}",

            $contract

        );



        return redirect()->back()->with('success', 'Terima kasih! Pesanan sesi pemotretan telah selesai dikonfirmasi. Silakan berikan ulasan dan rating untuk fotografer.')

            ->with('open_review_modal', true)

            ->with('review_contract_id', $contract->id);

    }



    /**

     * Ajukan Permintaan Revisi Hasil Foto oleh Klien.

     */

    public function requestRevision(Request $request, $id)

    {

        $request->validate([

            'catatan_revisi' => 'required|string|max:1000',

        ], [

            'catatan_revisi.required' => 'Wajib mengisi rincian permintaan revisi yang diinginkan.',

        ]);



        $clientProfile = RoleClient::where('id_user', auth()->id())->firstOrFail();

        $contract = Contract::with(['photographer.user', 'bookingDetail'])

            ->where('id_client', $clientProfile->id)

            ->findOrFail($id);

        if ($contract->status_contract === 'completed' && $contract->is_validated_client) {
            return redirect()->back()->with('error', 'Sesi foto yang telah dikonfirmasi selesai tidak dapat diajukan revisi.');
        }

        if ($contract->status_contract === 'cancelled') {
            return redirect()->back()->with('error', 'Sesi foto yang telah dibatalkan tidak dapat diajukan revisi.');
        }

        $contract->update([

            'is_validated_client' => false,

            'status_payout' => 'holding',

            'payout_notes' => 'Permintaan revisi diajukan Klien: ' . $request->catatan_revisi,

        ]);



        if ($contract->bookingDetail) {

            $contract->bookingDetail->update([

                'catatan_fotografer' => 'Klien meminta revisi: ' . $request->catatan_revisi

            ]);

        }



        $code = 'KTRK-' . str_pad($contract->id, 5, '0', STR_PAD_LEFT);



        // Notifikasi ke Fotografer

        if ($contract->photographer && $contract->photographer->id_user) {

            \App\Models\Notification::send(

                $contract->photographer->id_user,

                'Permintaan Revisi Hasil Foto',

                "Klien {$clientProfile->nama} mengajukan permintaan revisi untuk sesi {$code}. Catatan: {$request->catatan_revisi}",

                'revision_requested',

                url('/photographer/bookings')

            );

        }



        // Record Activity Log

        \App\Models\ActivityLog::record(

            'revision_requested_by_client',

            "Klien {$clientProfile->nama} meminta revisi untuk sesi {$code}: {$request->catatan_revisi}",

            $contract,

            ['catatan_revisi' => $request->catatan_revisi]

        );



        return redirect()->back()->with('success', 'Permintaan revisi berhasil dikirimkan ke fotografer mitra.');

    }



    /**

     * Ajukan Permintaan Jadwal Ulang Sesi Foto oleh Klien.

     */

    public function requestReschedule(Request $request, $id)

    {

        $request->validate([

            'reschedule_date' => 'required|date|after_or_equal:today',

            'reschedule_time' => 'required',

            'reschedule_reason' => 'required|string|max:1000',

        ], [

            'reschedule_date.required' => 'Tanggal jadwal baru wajib diisi.',

            'reschedule_date.after_or_equal' => 'Tanggal jadwal baru minimal hari ini.',

            'reschedule_time.required' => 'Jam mulai baru wajib dipilih.',

            'reschedule_reason.required' => 'Alasan perubahan jadwal wajib diisi.',

        ]);



        $clientProfile = RoleClient::where('id_user', auth()->id())->firstOrFail();

        $contract = Contract::with(['photographer.user', 'bookingDetail'])

            ->where('id_client', $clientProfile->id)

            ->findOrFail($id);



        if (in_array($contract->status_contract, ['completed', 'cancelled'])) {

            return redirect()->back()->with('error', 'Pesanan yang sudah selesai atau dibatalkan tidak dapat diubah jadwalnya.');

        }



        $newDate = $request->input('reschedule_date');

        $newTime = substr($request->input('reschedule_time'), 0, 5);

        $photographerId = $contract->id_photographer;



        // Validasi 0: Jam Kerja Mingguan Fotografer (Weekly Recurring Schedule)

        $dow = (int) \Carbon\Carbon::parse($newDate)->dayOfWeekIso;

        $workingHour = \App\Models\PhotographerWorkingHour::where('id_photographer', $photographerId)

            ->where('day_of_week', $dow)

            ->first();



        if ($workingHour) {

            if (!$workingHour->is_open) {

                return redirect()->back()->with('error', "Fotografer tidak beroperasi pada hari {$workingHour->day_name}. Silakan pilih hari operasional lainnya.");

            }

            if (!$workingHour->is_24_hours) {

                $buka = substr($workingHour->jam_buka, 0, 5);

                $tutup = substr($workingHour->jam_tutup, 0, 5);

                if ($newTime < $buka || $newTime > $tutup) {

                    return redirect()->back()->with('error', "Fotografer beroperasi dari jam {$buka} s/d {$tutup} WIB pada hari {$workingHour->day_name}. Jam {$newTime} WIB berada di luar jam operasional.");

                }

            }

        }



        $newStart = substr($newTime, 0, 5);

        $newEnd = date('H:i', strtotime($newStart . ' +4 hours'));



        // Validasi 1: Apakah tanggal/jam diblokir fotografer (interval overlap check)

        $blockedAvailabilities = \App\Models\PhotographerAvailability::where('id_photographer', $photographerId)

            ->where('tanggal', $newDate)

            ->get();



        foreach ($blockedAvailabilities as $blk) {

            if (!$blk->jam_mulai || !$blk->jam_selesai || ($blk->jam_mulai == '00:00:00' && $blk->jam_selesai == '23:59:59')) {

                return redirect()->back()->with('error', 'Fotografer sedang libur penuh pada tanggal ' . \Carbon\Carbon::parse($newDate)->translatedFormat('d F Y') . '. Silakan pilih tanggal lain.');

            }

            $blkStart = substr($blk->jam_mulai, 0, 5);

            $blkEnd = substr($blk->jam_selesai, 0, 5);

            if ($newStart < $blkEnd && $newEnd > $blkStart) {

                return redirect()->back()->with('error', 'Sesi jam ' . $newStart . ' - ' . $newEnd . ' WIB pada tanggal tersebut berbenturan dengan penutupan jadwal fotografer (' . ($blk->keterangan ?: 'Tutup Sesi: ' . $blkStart . '-' . $blkEnd) . '). Silakan pilih jam lain.');

            }

        }



        // Validasi 2: Apakah fotografer sudah ada pesanan lain pada rentang jam tersebut (interval overlap check)

        $existingBookings = \App\Models\ContractBooking::whereHas('contract', function ($q) use ($photographerId, $contract) {

            $q->where('id_photographer', $photographerId)

              ->where('id', '!=', $contract->id)

              ->whereIn('status_contract', ['active', 'approved', 'pending_payment']);

        })->where('booking_date', $newDate)->get();



        foreach ($existingBookings as $eb) {

            $ebStart = substr($eb->jam_mulai ?: '08:00', 0, 5);

            $ebEnd = $eb->jam_selesai ? substr($eb->jam_selesai, 0, 5) : date('H:i', strtotime($ebStart . ' +' . ((int) $eb->durasi ?: 4) . ' hours'));

            if ($newStart < $ebEnd && $newEnd > $ebStart) {

                return redirect()->back()->with('error', 'Jadwal sesi jam ' . $newStart . ' - ' . $newEnd . ' WIB berbenturan dengan pesanan klien lain (' . $ebStart . ' - ' . $ebEnd . ' WIB). Silakan pilih jam lain.');

            }

        }



        $contract->bookingDetail->update([

            'reschedule_by' => 'client',

            'reschedule_date' => $newDate,

            'reschedule_time' => $newTime,

            'reschedule_reason' => $request->input('reschedule_reason'),

            'reschedule_status' => 'pending',

        ]);



        $code = 'KTRK-' . str_pad($contract->id, 5, '0', STR_PAD_LEFT);



        // Notifikasi ke Fotografer

        if ($contract->photographer && $contract->photographer->id_user) {

            $formattedDate = \Carbon\Carbon::parse($newDate)->translatedFormat('d M Y');

            \App\Models\Notification::send(

                $contract->photographer->id_user,

                'Pengajuan Jadwal Ulang Sesi',

                "Klien {$clientProfile->nama} mengajukan perubahan jadwal sesi {$code} ke tanggal {$formattedDate} jam {$newTime} WIB. Alasan: {$request->reschedule_reason}",

                'booking_reschedule',

                url('/photographer/bookings')

            );

        }



        \App\Models\ActivityLog::record(

            'reschedule_requested_by_client',

            "Klien {$clientProfile->nama} mengajukan jadwal ulang sesi {$code} ke {$newDate} jam {$newTime} WIB. Alasan: {$request->reschedule_reason}",

            $contract,

            ['reschedule_date' => $newDate, 'reschedule_time' => $newTime, 'reason' => $request->reschedule_reason]

        );



        return redirect()->back()->with('success', 'Pengajuan jadwal ulang berhasil dikirimkan ke fotografer mitra. Menunggu konfirmasi persetujuan.');

    }



    /**

     * Tanggapi Pengajuan Jadwal Ulang dari Fotografer oleh Klien.

     */

    public function respondReschedule(Request $request, $id)

    {

        $request->validate([

            'action' => 'required|in:accept,reject',

        ]);



        $clientProfile = RoleClient::where('id_user', auth()->id())->firstOrFail();

        $contract = Contract::with(['photographer.user', 'bookingDetail'])

            ->where('id_client', $clientProfile->id)

            ->findOrFail($id);



        $booking = $contract->bookingDetail;

        if (!$booking || $booking->reschedule_status !== 'pending' || $booking->reschedule_by !== 'photographer') {

            return redirect()->back()->with('error', 'Tidak ada pengajuan jadwal ulang yang perlu ditanggapi.');

        }



        $code = 'KTRK-' . str_pad($contract->id, 5, '0', STR_PAD_LEFT);

        $action = $request->input('action');



        if ($action === 'accept') {

            $newDate = $booking->reschedule_date;

            $newTime = $booking->reschedule_time;

            $newStart = substr($newTime, 0, 5);

            $newEnd = date('H:i', strtotime($newStart . ' +4 hours'));



            // Validasi benturan sebelum menyetujui

            $conflict = \App\Models\ContractBooking::whereHas('contract', function ($q) use ($contract) {

                $q->where('id_photographer', $contract->id_photographer)

                  ->where('id', '!=', $contract->id)

                  ->whereIn('status_contract', ['active', 'approved', 'pending_payment']);

            })->where('booking_date', $newDate)->get()->first(function ($eb) use ($newStart, $newEnd) {

                $ebStart = substr($eb->jam_mulai ?: '08:00', 0, 5);

                $ebEnd = $eb->jam_selesai ? substr($eb->jam_selesai, 0, 5) : date('H:i', strtotime($ebStart . ' +' . ((int) $eb->durasi ?: 4) . ' hours'));

                return $newStart < $ebEnd && $newEnd > $ebStart;

            });



            if ($conflict) {

                return redirect()->back()->with('error', 'Jadwal yang diajukan telah terisi oleh pesanan klien lain. Silakan tolak atau ajukan waktu lain.');

            }



            $booking->update([

                'booking_date' => $newDate,

                'jam_mulai' => $newTime,

                'jam_selesai' => date('H:i', strtotime($newTime . ' +4 hours')),

                'reschedule_status' => 'accepted',

            ]);



            if ($contract->photographer && $contract->photographer->id_user) {

                $formattedDate = \Carbon\Carbon::parse($newDate)->translatedFormat('d M Y');

                \App\Models\Notification::send(

                    $contract->photographer->id_user,

                    'Jadwal Ulang Disetujui Klien',

                    "Klien {$clientProfile->nama} menyetujui pengajuan jadwal baru sesi {$code} pada tanggal {$formattedDate} jam {$newTime} WIB.",

                    'booking_reschedule_accepted',

                    url('/photographer/bookings')

                );

            }



            \App\Models\ActivityLog::record(

                'reschedule_accepted_by_client',

                "Klien {$clientProfile->nama} menyetujui perubahan jadwal sesi {$code} menjadi {$newDate} jam {$newTime} WIB",

                $contract

            );



            return redirect()->back()->with('success', 'Jadwal baru berhasil disetujui! Jadwal pemotretan telah diperbaharui.');

        } else {

            $booking->update([

                'reschedule_status' => 'rejected',

            ]);



            if ($contract->photographer && $contract->photographer->id_user) {

                \App\Models\Notification::send(

                    $contract->photographer->id_user,

                    'Pengajuan Jadwal Ulang Ditolak Klien',

                    "Klien {$clientProfile->nama} tidak dapat menyetujui perubahan jadwal untuk sesi {$code}. Jadwal sesi foto tetap mengacu pada tanggal sebelumnya.",

                    'booking_reschedule_rejected',

                    url('/photographer/bookings')

                );

            }



            \App\Models\ActivityLog::record(

                'reschedule_rejected_by_client',

                "Klien {$clientProfile->nama} menolak perubahan jadwal sesi {$code}",

                $contract

            );



            return redirect()->back()->with('info', 'Pengajuan jadwal ulang telah ditolak. Jadwal sesi foto tetap mengacu pada tanggal awal.');

        }

    }

}