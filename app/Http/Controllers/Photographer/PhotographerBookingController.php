<?php



namespace App\Http\Controllers\Photographer;



use App\Http\Controllers\Controller;

use App\Models\Contract;

use App\Models\Notification;

use App\Models\RolePhotographer;
use App\Services\BookingScheduleService;
use Illuminate\Http\Request;



class PhotographerBookingController extends Controller

{

    /**

     * Display Photographer Incoming Booking Orders.

     */

    public function index(Request $request)

    {

        $photographer = RolePhotographer::firstOrCreate(

            ['id_user' => auth()->id()],

            ['nama' => auth()->user()->nama]

        );

        // Jalankan auto-expire untuk pesanan fotografer yang kadaluarsa (waktu habis / sesi terlewati)
        app(\App\Services\BookingExpirationService::class)->expireOutdatedBookings(null, $photographer->id);



        $filterStatus = $request->input('status', 'all');



        $query = Contract::with([

            'client.user',

            'bookingDetail.service',

            'bookingDetail.province',

            'bookingDetail.city',

            'payments',

            'photographerClientReview',
            'client.receivedPhotographerReviews'

        ])

        ->where('id_photographer', $photographer->id)

        ->latest();



        if ($filterStatus !== 'all') {
            if ($filterStatus === 'pending_review' || $filterStatus === 'pending') {
                $query->whereNotIn('status_contract', ['cancelled', 'completed', 'active', 'pending_payment'])
                      ->where(function ($q) {
                          $q->where('is_validated_photographer', false)
                            ->orWhereHas('bookingDetail', fn($b) => $b->where('status_booking', 'pending'));
                      });
            } elseif ($filterStatus === 'pending_payment') {
                $query->whereNotIn('status_contract', ['cancelled', 'completed'])
                      ->where(function ($q) {
                          $q->where('status_contract', 'pending_payment')
                            ->orWhere(function ($sub) {
                                $sub->where('is_validated_photographer', true)
                                    ->whereHas('bookingDetail', fn($b) => $b->where('status_booking', 'confirmed'))
                                    ->where('status_contract', '!=', 'active');
                            });
                      });
            } elseif ($filterStatus === 'confirmed') {
                $query->where('status_contract', 'active')
                      ->whereHas('bookingDetail', fn($b) => $b->whereNull('hasil_foto_url'));
            } elseif ($filterStatus === 'pending_client') {
                $query->whereNotIn('status_contract', ['cancelled'])
                      ->where('is_validated_client', false)
                      ->whereHas('bookingDetail', fn($b) => $b->whereNotNull('hasil_foto_url'));
            } elseif ($filterStatus === 'completed') {
                $query->where('status_contract', 'completed')
                      ->where('is_validated_client', true);
            } elseif ($filterStatus === 'cancelled') {
                $query->where('status_contract', 'cancelled')
                      ->where(function ($q) {
                          $q->whereDoesntHave('cancellation')
                            ->orWhereHas('cancellation', function ($cq) {
                                $cq->where('alasan', 'not like', '%kadaluarsa%')
                                   ->where('alasan', 'not like', '%terlewati%');
                            });
                      });
            } elseif ($filterStatus === 'expired') {
                $query->where('status_contract', 'cancelled')
                      ->whereHas('cancellation', function ($cq) {
                          $cq->where(function ($sub) {
                              $sub->where('alasan', 'like', '%kadaluarsa%')
                                  ->orWhere('alasan', 'like', '%terlewati%');
                          });
                      });
            }
        }

        $perPage = (int) $request->query('per_page', 10);
        if (!in_array($perPage, [10, 20, 50, 100])) $perPage = 10;
        $contracts = $query->paginate($perPage)->withQueryString();

        // Stats summary count
        $stats = [
            'all' => Contract::where('id_photographer', $photographer->id)->count(),
            'pending' => Contract::where('id_photographer', $photographer->id)->whereNotIn('status_contract', ['cancelled'])->whereHas('bookingDetail', fn($q) => $q->where('status_booking', 'pending'))->count(),
            'confirmed' => Contract::where('id_photographer', $photographer->id)->whereNotIn('status_contract', ['cancelled'])->whereHas('bookingDetail', fn($q) => $q->where('status_booking', 'confirmed'))->count(),
            'completed' => Contract::where('id_photographer', $photographer->id)->whereNotIn('status_contract', ['cancelled'])->whereHas('bookingDetail', fn($q) => $q->where('status_booking', 'completed'))->count(),
        ];



        // Pengingat Sesi Pintar H-1 & Hari-H untuk Fotografer

        $urgentSession = \App\Models\ContractBooking::whereHas('contract', function ($q) use ($photographer) {
                $q->where('id_photographer', $photographer->id)
                  ->where('status_contract', 'active');
            })

            ->upcomingUrgent()

            ->with(['contract.client.user', 'service'])

            ->first();

        if ($urgentSession && auth()->check()) {
            BookingScheduleService::dispatchUrgentSessionNotification($urgentSession, auth()->user(), 'photographer');
        }



        return view('photographer.bookings.index', compact('contracts', 'stats', 'filterStatus', 'photographer', 'urgentSession'));

    }



    /**

     * Update Booking Order Status (Confirm, Cancel, Complete).

     */

    public function updateStatus(Request $request, $id)

    {

        $request->validate([

            'status' => 'required|in:confirmed,cancelled,completed',

        ]);



        $photographer = RolePhotographer::firstOrCreate(

            ['id_user' => auth()->id()],

            ['nama' => auth()->user()->nama]

        );

        // Jalankan auto-expire untuk pesanan fotografer yang kadaluarsa (waktu habis / sesi terlewati)
        app(\App\Services\BookingExpirationService::class)->expireOutdatedBookings(null, $photographer->id);



        $contract = Contract::with(['client.user'])->where('id_photographer', $photographer->id)->findOrFail($id);

        if ($contract->status_contract === 'cancelled') {
            return redirect()->back()->with('error', 'Pesanan sesi foto yang telah dibatalkan tidak dapat diubah statusnya.');
        }

        if ($contract->status_contract === 'completed' && $request->input('status') !== 'completed') {
            return redirect()->back()->with('error', 'Pesanan sesi foto yang telah selesai tidak dapat dibatalkan atau diubah.');
        }

        $status = $request->input('status');



        if ($status === 'confirmed') {
            // Validasi jika jadwal sesi sudah lewat
            $booking = $contract->bookingDetail;
            if ($booking && $booking->booking_date) {
                $timeStr = $booking->jam_mulai ? substr($booking->jam_mulai, 0, 5) : '08:00';
                $sessionStart = \Carbon\Carbon::parse($booking->booking_date . ' ' . $timeStr);
                if ($sessionStart->lte(now())) {
                    app(\App\Services\BookingExpirationService::class)->checkAndExpireSingle($contract);
                    return redirect()->back()->with('error', 'Jadwal sesi foto telah terlewati dan tidak dapat disetujui.');
                }
            }

            $isPaid = $contract->payments()->where('payment_status', 'completed')->exists();
            $newContractStatus = $isPaid ? 'active' : 'pending_payment';

            $contract->bookingDetail()->update(['status_booking' => 'confirmed']);

            // Hitung batas waktu pembayaran dinamis (Dynamic Deadline ala SweetEscape / Traveloka)
            $deadline = $isPaid ? null : $contract->calculatePaymentDeadline();

            $contract->update([
                'status_contract' => $newContractStatus,
                'is_validated_photographer' => true,
                'expired_at' => $deadline,
            ]);

            $deadlineText = $contract->payment_deadline_description;

            $message = $isPaid
                ? 'Pesanan booking berhasil dikonfirmasi! Jadwal sesi foto telah aktif.'
                : 'Pesanan booking berhasil disetujui! Klien telah diberitahukan untuk menyelesaikan pembayaran tagihan.';

            \App\Models\ActivityLog::record(
                'booking_confirmed',
                "Fotografer {$photographer->nama} menyetujui booking sesi KTRK-" . str_pad($contract->id, 5, '0', STR_PAD_LEFT),
                $contract
            );

            if ($contract->client && $contract->client->id_user) {
                Notification::create([
                    'id_user' => $contract->client->id_user,
                    'type' => 'booking_confirmed',
                    'title' => 'Reservasi Disetujui Fotografer',
                    'message' => "Fotografer {$photographer->nama} telah menyetujui reservasi sesi foto Anda. Silakan selesaikan pembayaran tagihan {$deadlineText}.",
                    'data' => [
                        'contract_id' => $contract->id,
                        'url' => route('booking.invoice', $contract->id),
                    ],
                    'is_read' => false,
                ]);
            }

            // Model Hybrid: Kirim konfirmasi pesan langsung ke thread chat
            $thread = \App\Models\ChatBooking::where('id_client', $contract->id_client)
                ->where('id_photographer', $contract->id_photographer)
                ->first();

            if ($thread && auth()->check()) {
                $serviceTitle = $contract->bookingDetail?->service?->nama_layanan ?? 'sesi foto';
                $chatMsg = $isPaid
                    ? "Halo {$contract->client->nama}, saya telah mengonfirmasi reservasi {$serviceTitle}. Jadwal pemotretan telah aktif."
                    : "Halo {$contract->client->nama}, saya telah menyetujui reservasi {$serviceTitle}. Tagihan pembayaran telah aktif dan dapat diselesaikan {$deadlineText}.";

                \App\Models\ChatMessage::create([
                    'id_chat_booking' => $thread->id,
                    'id_sender' => auth()->id(),
                    'message' => $chatMsg,
                    'is_read' => false,
                ]);
            }
        } elseif ($status === 'cancelled') {

            $alasan = $request->input('alasan') ?: 'Fotografer tidak dapat menerima jadwal sesi pemotretan ini.';

            $isPaid = $contract->payments()->where('payment_status', 'completed')->exists();
            $actualPaid = (float) $contract->payments->where('payment_status', 'completed')->sum('payment_amount');
            $refundAmount = $isPaid ? ($actualPaid > 0 ? $actualPaid : ($contract->payment_type === 'dp' && $contract->jumlah_dp > 0 ? (float)$contract->jumlah_dp : (float)$contract->jumlah)) : 0;

            $contract->bookingDetail()->update(['status_booking' => 'cancelled']);

            $contractUpdates = ['status_contract' => 'cancelled'];

            if ($isPaid) {
                $contractUpdates['status_payout'] = 'holding';
                $contractUpdates['payout_notes'] = 'Dibatalkan oleh Fotografer (Menunggu refund Admin): ' . $alasan;
            } else {
                $contractUpdates['status_payout'] = null;
                $contractUpdates['expired_at'] = now();
                $contract->payments()->where('payment_status', 'pending')->update(['payment_status' => 'failed']);
            }

            $contract->update($contractUpdates);

            // Simpan ke log contract_cancellations
            \App\Models\ContractCancellation::create([
                'id_contract' => $contract->id,
                'alasan' => $alasan,
                'dibatalkan_oleh' => 'photographer',
                'refund_amount' => $refundAmount,
            ]);

            \App\Models\ActivityLog::record(
                'booking_rejected',
                "Fotografer {$photographer->nama} menolak/membatalkan booking sesi KTRK-" . str_pad($contract->id, 5, '0', STR_PAD_LEFT) . ". Alasan: {$alasan}",
                $contract,
                ['alasan' => $alasan, 'refund_amount' => $refundAmount]
            );



            $message = 'Pesanan booking telah ditolak / dibatalkan.';



            if ($contract->client && $contract->client->id_user) {

                Notification::create([

                    'id_user' => $contract->client->id_user,

                    'type' => 'booking_cancelled',

                    'title' => 'Jadwal Booking Dibatalkan',

                    'message' => "Pesanan booking KTRK-" . str_pad($contract->id, 5, '0', STR_PAD_LEFT) . " telah ditolak / dibatalkan oleh fotografer. Alasan: {$alasan}",

                    'data' => [

                        'contract_id' => $contract->id,

                        'url' => route('client.bookings'),

                    ],

                    'is_read' => false,

                ]);

            }

            // Model Hybrid: Beritahukan penolakan di thread chat
            $thread = \App\Models\ChatBooking::where('id_client', $contract->id_client)
                ->where('id_photographer', $contract->id_photographer)
                ->first();

            if ($thread && auth()->check()) {
                $contractCode = 'KTRK-' . str_pad($contract->id, 5, '0', STR_PAD_LEFT);
                \App\Models\ChatMessage::create([
                    'id_chat_booking' => $thread->id,
                    'id_sender' => auth()->id(),
                    'message' => "Mohon maaf {$contract->client->nama}, saya belum dapat menerima reservasi sesi {$contractCode}. Alasan: {$alasan}",
                    'is_read' => false,
                ]);
            }

        } elseif ($status === 'completed') {
            if ($contract->status_contract !== 'active') {
                return redirect()->back()->with('error', 'Pesanan sesi foto belum dapat ditandai selesai karena pembayaran belum terverifikasi.');
            }

            $contract->bookingDetail()->update(['status_booking' => 'completed']);

            $contractUpdates = ['status_contract' => 'completed'];

            $contract->update($contractUpdates);



            \App\Models\ActivityLog::record(

                'booking_completed',

                "Fotografer {$photographer->nama} menandai sesi KTRK-" . str_pad($contract->id, 5, '0', STR_PAD_LEFT) . " Selesai",

                $contract

            );



            $message = 'Sesi foto telah ditandai Selesai!';

        }



        return redirect()->back()->with('success', $message);

    }



    /**

     * Upload Google Drive / Cloud Link for Final Photo Gallery Delivery.

     */

    public function uploadGallery(Request $request, $id)

    {

        $request->validate([

            'hasil_foto_url' => 'required|url',

            'catatan_fotografer' => 'nullable|string|max:1000',

        ]);



        $photographer = RolePhotographer::firstOrCreate(

            ['id_user' => auth()->id()],

            ['nama' => auth()->user()->nama]

        );

        // Jalankan auto-expire untuk pesanan fotografer yang kadaluarsa (waktu habis / sesi terlewati)
        app(\App\Services\BookingExpirationService::class)->expireOutdatedBookings(null, $photographer->id);



        $contract = Contract::with(['client.user'])->where('id_photographer', $photographer->id)->findOrFail($id);

        if ($contract->status_contract === 'cancelled') {
            return redirect()->back()->with('error', 'Pesanan sesi foto yang telah dibatalkan tidak dapat diunggah galeri fotonya.');
        }

        if (!in_array($contract->status_contract, ['active', 'completed'])) {
            return redirect()->back()->with('error', 'Tautan galeri foto hanya dapat diunggah untuk pesanan yang telah aktif dan terverifikasi pembayarannya.');
        }

        $contract->bookingDetail()->update([
            'hasil_foto_url' => $request->input('hasil_foto_url'),
            'catatan_fotografer' => $request->input('catatan_fotografer'),
            'status_booking' => 'completed',
        ]);

        $contractUpdates = [
            'status_contract' => 'completed',
            'is_validated_client' => false,
        ];

        if (empty($contract->status_payout)) {
            $contractUpdates['status_payout'] = 'holding';
        }

        $contract->update($contractUpdates);



        // Kirim Notifikasi in-app ke Klien

        if ($contract->client && $contract->client->id_user) {

            Notification::create([

                'id_user' => $contract->client->id_user,

                'type' => 'gallery_delivered',

                'title' => 'Galeri Hasil Foto Siap Diunduh',

                'message' => "Fotografer {$photographer->nama} telah mengunggah tautan galeri hasil foto untuk sesi KTRK-" . str_pad($contract->id, 5, '0', STR_PAD_LEFT) . ". Silakan akses dan unduh hasilnya.",

                'data' => [

                    'contract_id' => $contract->id,

                    'url' => route('client.galleries'),

                ],

                'is_read' => false,

            ]);

        }



        \App\Models\ActivityLog::record(

            'gallery_uploaded',

            "Fotografer {$photographer->nama} mengunggah tautan galeri foto untuk sesi KTRK-" . str_pad($contract->id, 5, '0', STR_PAD_LEFT),

            $contract,

            ['hasil_foto_url' => $request->input('hasil_foto_url')]

        );



        return redirect()->back()->with('success', 'Tautan galeri foto berhasil diunggah & dikirimkan ke Klien!');

    }



    /**

     * Ajukan Permintaan Jadwal Ulang Sesi Foto oleh Fotografer.

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



        $photographer = RolePhotographer::firstOrCreate(

            ['id_user' => auth()->id()],

            ['nama' => auth()->user()->nama]

        );

        // Jalankan auto-expire untuk pesanan fotografer yang kadaluarsa (waktu habis / sesi terlewati)
        app(\App\Services\BookingExpirationService::class)->expireOutdatedBookings(null, $photographer->id);



        $contract = Contract::with(['client.user', 'bookingDetail'])

            ->where('id_photographer', $photographer->id)

            ->findOrFail($id);



        if (in_array($contract->status_contract, ['completed', 'cancelled'])) {
            return redirect()->back()->with('error', 'Pesanan yang sudah selesai atau dibatalkan tidak dapat diubah jadwalnya.');
        }

        if ($contract->bookingDetail && $contract->bookingDetail->reschedule_status === 'pending') {
            return redirect()->back()->with('error', 'Sudah ada pengajuan jadwal ulang yang sedang menunggu tanggapan.');
        }



        $newDate = $request->input('reschedule_date');

        $newTime = substr($request->input('reschedule_time'), 0, 5);



        $rescheduleCheck = BookingScheduleService::validateReschedule($contract, $newDate, $newTime);
        if ($rescheduleCheck['error'] !== null) {
            return redirect()->back()->with('error', $rescheduleCheck['error']);
        }

        $contract->bookingDetail->update([

            'reschedule_by' => 'photographer',

            'reschedule_date' => $newDate,

            'reschedule_time' => $newTime,

            'reschedule_reason' => $request->input('reschedule_reason'),

            'reschedule_status' => 'pending',

        ]);



        $code = 'KTRK-' . str_pad($contract->id, 5, '0', STR_PAD_LEFT);



        // Notifikasi ke Klien

        if ($contract->client && $contract->client->id_user) {

            $formattedDate = \Carbon\Carbon::parse($newDate)->translatedFormat('d M Y');

            \App\Models\Notification::send(

                $contract->client->id_user,

                'Fotografer Mengajukan Jadwal Ulang',

                "Fotografer {$photographer->nama} mengajukan perubahan jadwal sesi {$code} ke tanggal {$formattedDate} jam {$newTime} WIB. Alasan: {$request->reschedule_reason}",

                'booking_reschedule',

                url('/client/bookings')

            );

        }



        \App\Models\ActivityLog::record(

            'reschedule_requested_by_photographer',

            "Fotografer {$photographer->nama} mengajukan jadwal ulang sesi {$code} ke {$newDate} jam {$newTime} WIB. Alasan: {$request->reschedule_reason}",

            $contract,

            ['reschedule_date' => $newDate, 'reschedule_time' => $newTime, 'reason' => $request->reschedule_reason]

        );



        return redirect()->back()->with('success', 'Pengajuan perubahan jadwal berhasil dikirimkan ke Klien. Menunggu konfirmasi persetujuan.');

    }



    /**

     * Tanggapi Pengajuan Jadwal Ulang dari Klien oleh Fotografer.

     */

    public function respondReschedule(Request $request, $id)

    {

        $request->validate([

            'action' => 'required|in:accept,reject',

        ]);



        $photographer = RolePhotographer::firstOrCreate(

            ['id_user' => auth()->id()],

            ['nama' => auth()->user()->nama]

        );

        // Jalankan auto-expire untuk pesanan fotografer yang kadaluarsa (waktu habis / sesi terlewati)
        app(\App\Services\BookingExpirationService::class)->expireOutdatedBookings(null, $photographer->id);



        $contract = Contract::with(['client.user', 'bookingDetail'])

            ->where('id_photographer', $photographer->id)

            ->findOrFail($id);



        $booking = $contract->bookingDetail;

        if (!$booking || $booking->reschedule_status !== 'pending' || $booking->reschedule_by !== 'client') {

            return redirect()->back()->with('error', 'Tidak ada pengajuan jadwal ulang yang perlu ditanggapi.');

        }



        $code = 'KTRK-' . str_pad($contract->id, 5, '0', STR_PAD_LEFT);

        $action = $request->input('action');



        if ($action === 'accept') {

            $newDate = $booking->reschedule_date;
            $newTime = substr((string) $booking->reschedule_time, 0, 5);

            $rescheduleCheck = BookingScheduleService::validateReschedule($contract, $newDate, $newTime);
            if ($rescheduleCheck['error'] !== null) {
                return redirect()->back()->with('error', $rescheduleCheck['error']);
            }

            $booking->update([
                'booking_date' => $newDate,
                'booking_end_date' => $rescheduleCheck['new_end_date'],
                'jam_mulai' => $rescheduleCheck['new_start_time'],
                'jam_selesai' => $rescheduleCheck['new_end_time'],
                'reschedule_status' => 'accepted',
            ]);

            $contract->refresh();

            if (in_array($contract->status_contract, ['pending_payment', 'draft', 'approved'])) {
                $contract->update([
                    'expired_at' => $contract->calculatePaymentDeadline(),
                ]);
            }

            if ($contract->client && $contract->client->id_user) {

                $formattedDate = \Carbon\Carbon::parse($newDate)->translatedFormat('d M Y');

                \App\Models\Notification::send(

                    $contract->client->id_user,

                    'Jadwal Ulang Disetujui Fotografer',

                    "Fotografer {$photographer->nama} telah menyetujui jadwal baru sesi {$code} pada tanggal {$formattedDate} jam {$newTime} WIB.",

                    'booking_reschedule_accepted',

                    url('/client/bookings')

                );

            }



            \App\Models\ActivityLog::record(

                'reschedule_accepted_by_photographer',

                "Fotografer {$photographer->nama} menyetujui jadwal ulang sesi {$code} menjadi {$newDate} jam {$newTime} WIB",

                $contract

            );



            return redirect()->back()->with('success', 'Jadwal baru berhasil disetujui! Jadwal pemotretan telah diperbaharui.');

        } else {

            $booking->update([

                'reschedule_status' => 'rejected',

            ]);



            if ($contract->client && $contract->client->id_user) {

                \App\Models\Notification::send(

                    $contract->client->id_user,

                    'Pengajuan Jadwal Ulang Ditolak Fotografer',

                    "Fotografer {$photographer->nama} tidak dapat menyetujui perubahan jadwal untuk sesi {$code}. Jadwal sesi foto tetap mengacu pada tanggal sebelumnya.",

                    'booking_reschedule_rejected',

                    url('/client/bookings')

                );

            }



            \App\Models\ActivityLog::record(

                'reschedule_rejected_by_photographer',

                "Fotografer {$photographer->nama} menolak perubahan jadwal sesi {$code}",

                $contract

            );



            return redirect()->back()->with('info', 'Pengajuan jadwal ulang telah ditolak. Jadwal sesi foto tetap mengacu pada tanggal awal.');

        }

    }



    /**

     * Berikan Penilaian & Review Performa Klien oleh Fotografer.

     */

    public function rateClient(Request $request, $id)

    {

        $request->validate([

            'rating' => 'required|integer|min:1|max:5',

            'ulasan' => 'nullable|string|max:1000',

            'ketepatan_waktu' => 'required|integer|min:1|max:5',

            'komunikasi' => 'required|integer|min:1|max:5',

            'kerjasama' => 'required|integer|min:1|max:5',

        ], [

            'rating.required' => 'Nilai kepuasan sesi (1-5) wajib dipilih.',

            'rating.min' => 'Rating minimal 1 bintang.',

            'rating.max' => 'Rating maksimal 5 bintang.',

        ]);



        $photographer = RolePhotographer::where('id_user', auth()->id())->firstOrFail();

        $contract = Contract::with(['client.user'])->where('id_photographer', $photographer->id)->findOrFail($id);



        if ($contract->status_contract !== 'completed') {

            return redirect()->back()->with('error', 'Penilaian klien hanya dapat dilakukan untuk pesanan sesi yang telah selesai (completed).');

        }



        \App\Models\PhotographerClientReview::updateOrCreate(

            ['id_contract' => $contract->id],

            [

                'id_photographer' => $photographer->id,

                'id_client' => $contract->id_client,

                'rating' => (int) $request->rating,

                'ulasan' => $request->ulasan ? trim($request->ulasan) : null,

                'ketepatan_waktu' => (int) $request->ketepatan_waktu,

                'komunikasi' => (int) $request->komunikasi,

                'kerjasama' => (int) $request->kerjasama,

            ]

        );



        // Kirim Notifikasi in-app ke Klien

        if ($contract->client && $contract->client->user) {

            \App\Models\Notification::send(

                $contract->client->user->id,

                'Ulasan dari Fotografer',

                "Fotografer {$photographer->nama} telah memberikan ulasan penilaian performa sesi foto untuk pesanan KTRK-" . str_pad($contract->id, 5, '0', STR_PAD_LEFT) . " ({$request->rating}/5 Bintang).",

                'client_rated',

                route('client.bookings')

            );

        }



        return redirect()->back()->with('success', 'Penilaian performa klien berhasil disimpan!');

    }

    /**
     * Hitung Jumlah Pesanan Booking Pending yang Memerlukan Tindakan Fotografer (Real-Time Badge).
     */
    public function pendingCount(Request $request)
    {
        $photographer = RolePhotographer::where('id_user', auth()->id())->first();
        if (!$photographer) {
            return response()->json(['pending_count' => 0]);
        }

        $count = Contract::where('id_photographer', $photographer->id)
            ->whereNotIn('status_contract', ['cancelled'])
            ->whereHas('bookingDetail', function ($q) {
                $q->where('status_booking', 'pending')
                  ->orWhere(function ($rq) {
                      $rq->where('reschedule_status', 'pending')
                         ->where('reschedule_by', 'client');
                  });
            })
            ->count();

        return response()->json(['pending_count' => $count]);
    }

}