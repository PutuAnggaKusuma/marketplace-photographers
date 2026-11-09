<?php



namespace App\Http\Controllers\Photographer;



use App\Http\Controllers\Controller;

use App\Models\Contract;

use App\Models\Notification;

use App\Models\RolePhotographer;

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

                $query->whereHas('bookingDetail', function ($q) {

                    $q->where('status_booking', 'cancelled');

                });

            }

        }



        $perPage = (int) $request->query('per_page', 10);

        if (!in_array($perPage, [10, 20, 50, 100])) $perPage = 10;

        $contracts = $query->paginate($perPage)->withQueryString();



        // Stats summary count

        $stats = [

            'all' => Contract::where('id_photographer', $photographer->id)->count(),

            'pending' => Contract::where('id_photographer', $photographer->id)->whereHas('bookingDetail', fn($q) => $q->where('status_booking', 'pending'))->count(),

            'confirmed' => Contract::where('id_photographer', $photographer->id)->whereHas('bookingDetail', fn($q) => $q->where('status_booking', 'confirmed'))->count(),

            'completed' => Contract::where('id_photographer', $photographer->id)->whereHas('bookingDetail', fn($q) => $q->where('status_booking', 'completed'))->count(),

        ];



        // Pengingat Sesi Pintar H-1 & Hari-H untuk Fotografer

        $urgentSession = \App\Models\ContractBooking::whereHas('contract', function ($q) use ($photographer) {

                $q->where('id_photographer', $photographer->id)

                  ->whereNotIn('status_contract', ['cancelled', 'completed']);

            })

            ->upcomingUrgent()

            ->with(['contract.client.user', 'service'])

            ->first();



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



        $contract = Contract::with(['client.user'])->where('id_photographer', $photographer->id)->findOrFail($id);

        if ($contract->status_contract === 'cancelled') {
            return redirect()->back()->with('error', 'Pesanan sesi foto yang telah dibatalkan tidak dapat diubah statusnya.');
        }

        if ($contract->status_contract === 'completed' && $request->input('status') !== 'completed') {
            return redirect()->back()->with('error', 'Pesanan sesi foto yang telah selesai tidak dapat dibatalkan atau diubah.');
        }

        $status = $request->input('status');



        if ($status === 'confirmed') {

            $contract->bookingDetail()->update(['status_booking' => 'confirmed']);

            $contract->update([

                'status_contract' => 'active',

                'is_validated_photographer' => true,

            ]);

            $message = 'Pesanan booking berhasil dikonfirmasi! Klien telah diberitahukan.';



            \App\Models\ActivityLog::record(

                'booking_confirmed',

                "Fotografer {$photographer->nama} mengonfirmasi booking sesi KTRK-" . str_pad($contract->id, 5, '0', STR_PAD_LEFT),

                $contract

            );



            if ($contract->client && $contract->client->id_user) {

                Notification::create([

                    'id_user' => $contract->client->id_user,

                    'type' => 'booking_confirmed',

                    'title' => 'Jadwal Booking Dikonfirmasi',

                    'message' => "Fotografer {$photographer->nama} telah mengonfirmasi jadwal pemotretan sesi KTRK-" . str_pad($contract->id, 5, '0', STR_PAD_LEFT) . ".",

                    'data' => [

                        'contract_id' => $contract->id,

                        'url' => route('client.bookings'),

                    ],

                    'is_read' => false,

                ]);

            }

        } elseif ($status === 'cancelled') {

            $alasan = $request->input('alasan') ?: 'Fotografer tidak dapat menerima jadwal sesi pemotretan ini.';

            $isPaid = in_array($contract->status_payout, ['holding', 'ready']);



            $contract->bookingDetail()->update(['status_booking' => 'cancelled']);

            

            $contractUpdates = ['status_contract' => 'cancelled'];

            if ($isPaid) {

                $contractUpdates['status_payout'] = 'refunded';

                $contractUpdates['payout_notes'] = 'Dibatalkan oleh Fotografer: ' . $alasan;

            }

            $contract->update($contractUpdates);



            // Simpan ke log contract_cancellations

            \App\Models\ContractCancellation::create([

                'id_contract' => $contract->id,

                'alasan' => $alasan,

                'dibatalkan_oleh' => 'photographer',

                'refund_amount' => $isPaid ? $contract->jumlah : 0,

            ]);



            \App\Models\ActivityLog::record(

                'booking_rejected',

                "Fotografer {$photographer->nama} menolak/membatalkan booking sesi KTRK-" . str_pad($contract->id, 5, '0', STR_PAD_LEFT) . ". Alasan: {$alasan}",

                $contract,

                ['alasan' => $alasan, 'refund_amount' => $isPaid ? $contract->jumlah : 0]

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

        } elseif ($status === 'completed') {

            $contract->bookingDetail()->update(['status_booking' => 'completed']);

            $contractUpdates = ['status_contract' => 'completed'];

            if ($contract->status_payout === 'holding' || empty($contract->status_payout)) {

                $contractUpdates['status_payout'] = 'ready';

            }

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



        $contract = Contract::with(['client.user'])->where('id_photographer', $photographer->id)->findOrFail($id);

        if ($contract->status_contract === 'cancelled') {
            return redirect()->back()->with('error', 'Pesanan sesi foto yang telah dibatalkan tidak dapat diunggah galeri fotonya.');
        }

        $contract->bookingDetail()->update([

            'hasil_foto_url' => $request->input('hasil_foto_url'),

            'catatan_fotografer' => $request->input('catatan_fotografer'),

            'status_booking' => 'completed',

        ]);



        $contractUpdates = [

            'status_contract' => 'completed',

            'is_validated_client' => false,

            'status_payout' => 'holding', // Escrow ditahan hingga klien mengonfirmasi penerimaan hasil foto

        ];

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



        $contract = Contract::with(['client.user', 'bookingDetail'])

            ->where('id_photographer', $photographer->id)

            ->findOrFail($id);



        if (in_array($contract->status_contract, ['completed', 'cancelled'])) {

            return redirect()->back()->with('error', 'Pesanan yang sudah selesai atau dibatalkan tidak dapat diubah jadwalnya.');

        }



        $newDate = $request->input('reschedule_date');

        $newTime = substr($request->input('reschedule_time'), 0, 5);



        // Validasi: Jam Kerja Mingguan Fotografer Sendiri

        $dow = (int) \Carbon\Carbon::parse($newDate)->dayOfWeekIso;

        $workingHour = \App\Models\PhotographerWorkingHour::where('id_photographer', $photographer->id)

            ->where('day_of_week', $dow)

            ->first();



        if ($workingHour) {

            if (!$workingHour->is_open) {

                return redirect()->back()->with('error', "Anda mengatur hari {$workingHour->day_name} sebagai hari libur operasional. Silakan pilih hari lain atau sesuaikan jam kerja di menu Jadwal.");

            }

            if (!$workingHour->is_24_hours) {

                $buka = substr($workingHour->jam_buka, 0, 5);

                $tutup = substr($workingHour->jam_tutup, 0, 5);

                if ($newTime < $buka || $newTime > $tutup) {

                    return redirect()->back()->with('error', "Jam {$newTime} WIB berada di luar jam kerja Anda pada hari {$workingHour->day_name} ({$buka} - {$tutup} WIB).");

                }

            }

        }



        $newStart = substr($newTime, 0, 5);

        $newEnd = date('H:i', strtotime($newStart . ' +4 hours'));



        // Validasi: Apakah fotografer sudah ada pesanan lain pada rentang jam tersebut (interval overlap check)

        $existingBookings = \App\Models\ContractBooking::whereHas('contract', function ($q) use ($photographer, $contract) {

            $q->where('id_photographer', $photographer->id)

              ->where('id', '!=', $contract->id)

              ->whereIn('status_contract', ['active', 'approved', 'pending_payment']);

        })->where('booking_date', $newDate)->get();



        foreach ($existingBookings as $eb) {

            $ebStart = substr($eb->jam_mulai ?: '08:00', 0, 5);

            $ebEnd = $eb->jam_selesai ? substr($eb->jam_selesai, 0, 5) : date('H:i', strtotime($ebStart . ' +' . ((int) $eb->durasi ?: 4) . ' hours'));

            if ($newStart < $ebEnd && $newEnd > $ebStart) {

                return redirect()->back()->with('error', 'Anda sudah memiliki jadwal sesi lain pada jam ' . $newStart . ' - ' . $newEnd . ' WIB di tanggal tersebut (' . $ebStart . ' - ' . $ebEnd . ' WIB). Silakan pilih jam lain.');

            }

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

            $newTime = $booking->reschedule_time;

            $newStart = substr($newTime, 0, 5);

            $newEnd = date('H:i', strtotime($newStart . ' +4 hours'));



            // Validasi benturan sebelum menyetujui

            $conflict = \App\Models\ContractBooking::whereHas('contract', function ($q) use ($contract, $photographer) {

                $q->where('id_photographer', $photographer->id)

                  ->where('id', '!=', $contract->id)

                  ->whereIn('status_contract', ['active', 'approved', 'pending_payment']);

            })->where('booking_date', $newDate)->get()->first(function ($eb) use ($newStart, $newEnd) {

                $ebStart = substr($eb->jam_mulai ?: '08:00', 0, 5);

                $ebEnd = $eb->jam_selesai ? substr($eb->jam_selesai, 0, 5) : date('H:i', strtotime($ebStart . ' +' . ((int) $eb->durasi ?: 4) . ' hours'));

                return $newStart < $ebEnd && $newEnd > $ebStart;

            });



            if ($conflict) {

                return redirect()->back()->with('error', 'Gagal menyetujui: Anda telah memiliki pesanan lain pada jam tersebut. Silakan minta klien memilih waktu lain.');

            }



            $booking->update([

                'booking_date' => $newDate,

                'jam_mulai' => $newTime,

                'jam_selesai' => date('H:i', strtotime($newTime . ' +4 hours')),

                'reschedule_status' => 'accepted',

            ]);



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

}