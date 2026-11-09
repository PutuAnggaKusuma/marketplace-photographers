<?php



namespace App\Http\Controllers\Public;



use App\Http\Controllers\Controller;

use App\Models\Contract;

use App\Models\ContractBooking;

use App\Models\Payment;

use App\Models\PhotographerService;

use App\Models\RoleClient;

use App\Models\RolePhotographer;

use App\Models\ServiceDetail;

use Illuminate\Http\Request;

use Illuminate\Support\Str;

use Laravolt\Indonesia\Models\Province;



class BookingController extends Controller

{

    /**

     * Display Booking Form for a Photographer & Service Package.

     */

    public function create(Request $request)

    {

        $photographerId = $request->input('photographer_id');

        if (!$photographerId) {

            $photographer = RolePhotographer::firstOrFail();

        } else {

            $photographer = RolePhotographer::with(['services.details', 'user', 'province', 'city'])->findOrFail($photographerId);

        }



        $allServices = $photographer->services()->with('details')->get();



        $serviceId = $request->input('service_id', old('service_id'));

        $service = null;

        if ($serviceId) {

            $service = $allServices->firstWhere('id', $serviceId);

        }



        $provinces = Province::orderBy('name', 'asc')->get();



        $preselectedDate = $request->input('tanggal', date('Y-m-d', strtotime('+2 days')));



        // Ensure working hours are initialized

        \App\Models\PhotographerWorkingHour::initializeDefaults($photographer->id);

        $workingHours = \App\Models\PhotographerWorkingHour::where('id_photographer', $photographer->id)

            ->get()

            ->keyBy('day_of_week')

            ->map(function ($wh) {

                return [

                    'day_of_week' => (int) $wh->day_of_week,

                    'day_name' => $wh->day_name,

                    'is_open' => (bool) $wh->is_open,

                    'is_24_hours' => (bool) $wh->is_24_hours,

                    'jam_buka' => substr($wh->jam_buka, 0, 5),

                    'jam_tutup' => substr($wh->jam_tutup, 0, 5),

                ];

            });



        // Full-day and partial-day blocks

        $availabilities = \App\Models\PhotographerAvailability::where('id_photographer', $photographer->id)

            ->where('tanggal', '>=', date('Y-m-d'))

            ->get()

            ->map(function ($a) {

                $isFullDay = !$a->jam_mulai || !$a->jam_selesai || ($a->jam_mulai == '00:00:00' && $a->jam_selesai == '23:59:59');

                return [

                    'tanggal' => $a->tanggal,

                    'is_full_day' => $isFullDay,

                    'jam_mulai' => $a->jam_mulai ? substr($a->jam_mulai, 0, 5) : null,

                    'jam_selesai' => $a->jam_selesai ? substr($a->jam_selesai, 0, 5) : null,

                    'keterangan' => $a->keterangan ?: 'Libur / Tutup',

                ];

            });



        // Blocked full-day dates

        $blockedDates = $availabilities->where('is_full_day', true)->pluck('tanggal')->values()->toArray();



        // Booked sessions with times

        $bookedSlots = \App\Models\ContractBooking::whereHas('contract', function ($q) use ($photographer) {

            $q->where('id_photographer', $photographer->id)

              ->whereIn('status_contract', ['active', 'approved', 'pending_payment']);

        })->where('booking_date', '>=', date('Y-m-d'))

          ->get()

          ->map(function ($b) {

              $start = substr($b->jam_mulai ?: '08:00', 0, 5);

              $end = $b->jam_selesai ? substr($b->jam_selesai, 0, 5) : null;

              return [

                  'date' => $b->booking_date,

                  'jam_mulai' => $start,

                  'jam_selesai' => $end,

                  'durasi' => (int) $b->durasi ?: 2,

              ];

          });



        // Booked dates

        $bookedDates = $bookedSlots->pluck('date')->unique()->values()->toArray();



        return view('public.booking.create', compact('photographer', 'service', 'allServices', 'provinces', 'preselectedDate', 'blockedDates', 'bookedDates', 'workingHours', 'availabilities', 'bookedSlots'));

    }



    /**

     * Store New Booking Reservation & Generate Payment Invoice.

     */

    public function store(Request $request)

    {

        $request->validate([

            'photographer_id' => 'required|exists:role_photographers,id',

            'service_id' => 'required|exists:photographer_services,id',

            'tanggal_sesi' => 'required|date|after_or_equal:today',

            'jam_mulai' => 'required',

            'lokasi_acara' => 'required|string|max:500',

            'province_code' => 'nullable|string',

            'city_code' => 'nullable|string',

            'catatan_khusus' => 'nullable|string',

            'selected_features' => 'nullable|array',

        ]);



        $tanggalSesi = $request->input('tanggal_sesi');

        $photographerId = $request->input('photographer_id');



        $jamMulai = $request->input('jam_mulai', '08:00');



        // Validasi 0: Jam Kerja Mingguan / Harian Fotografer (Weekly Recurring Schedule)

        $dow = (int) \Carbon\Carbon::parse($tanggalSesi)->dayOfWeekIso; // 1 = Senin, ..., 7 = Minggu

        $workingHour = \App\Models\PhotographerWorkingHour::where('id_photographer', $photographerId)

            ->where('day_of_week', $dow)

            ->first();



        if ($workingHour) {

            if (!$workingHour->is_open) {

                return redirect()->back()->withInput()->with('error', "Fotografer tidak beroperasi pada hari {$workingHour->day_name}. Silakan pilih hari operasional lainnya.");

            }

            if (!$workingHour->is_24_hours) {

                $buka = substr($workingHour->jam_buka, 0, 5);

                $tutup = substr($workingHour->jam_tutup, 0, 5);

                $bookingTime = substr($jamMulai, 0, 5);

                if ($bookingTime < $buka || $bookingTime > $tutup) {

                    return redirect()->back()->withInput()->with('error', "Fotografer beroperasi dari jam {$buka} s/d {$tutup} WIB pada hari {$workingHour->day_name}. Jam {$bookingTime} WIB berada di luar jam operasional.");

                }

            }

        }



        $durasi = (int) $request->input('durasi', 2);

        if ($durasi < 1) $durasi = 2;

        $bookingStart = substr($jamMulai, 0, 5);

        $bookingEnd = date('H:i', strtotime($bookingStart . ' +' . $durasi . ' hours'));



        // Validasi 1: Fotografer memblokir jadwal (seharian atau slot jam tertentu - interval overlap check)

        $blockedAvailabilities = \App\Models\PhotographerAvailability::where('id_photographer', $photographerId)

            ->where('tanggal', $tanggalSesi)

            ->get();


        foreach ($blockedAvailabilities as $blk) {

            // Jika blokir seharian penuh

            if (!$blk->jam_mulai || !$blk->jam_selesai || ($blk->jam_mulai == '00:00:00' && $blk->jam_selesai == '23:59:59')) {

                return redirect()->back()->withInput()->with('error', 'Fotografer sedang libur / tidak menerima sesi foto pada tanggal tersebut (' . ($blk->keterangan ?: 'Libur Penuh') . '). Silakan pilih tanggal lain.');

            }

            // Jika blokir jam tertentu (interval overlap check)

            $blkStart = substr($blk->jam_mulai, 0, 5);

            $blkEnd = substr($blk->jam_selesai, 0, 5);

            if ($bookingStart < $blkEnd && $bookingEnd > $blkStart) {

                return redirect()->back()->withInput()->with('error', 'Sesi jam ' . $bookingStart . ' - ' . $bookingEnd . ' WIB pada tanggal tersebut berbenturan dengan penutupan jadwal fotografer (' . ($blk->keterangan ?: 'Tutup Sesi: ' . $blkStart . '-' . $blkEnd) . '). Silakan pilih jam atau tanggal lain.');

            }

        }



        // Validasi 2: Jadwal fotografer sudah terisi oleh pesanan booking lain (interval overlap check)

        $existingBookings = \App\Models\ContractBooking::whereHas('contract', function ($q) use ($photographerId) {

            $q->where('id_photographer', $photographerId)

              ->whereIn('status_contract', ['active', 'approved', 'pending_payment']);

        })->where('booking_date', $tanggalSesi)->get();



        foreach ($existingBookings as $eb) {

            $ebStart = substr($eb->jam_mulai ?: '08:00', 0, 5);

            $ebEnd = $eb->jam_selesai ? substr($eb->jam_selesai, 0, 5) : date('H:i', strtotime($ebStart . ' +' . ((int) $eb->durasi ?: 4) . ' hours'));

            if ($bookingStart < $ebEnd && $bookingEnd > $ebStart) {

                return redirect()->back()->withInput()->with('error', 'Jadwal sesi foto jam ' . $bookingStart . ' - ' . $bookingEnd . ' WIB berbenturan dengan pesanan klien lain (' . $ebStart . ' - ' . $ebEnd . ' WIB). Silakan pilih jam lain.');

            }

        }



        $service = PhotographerService::with('details')->findOrFail($request->input('service_id'));

        $totalAmount = $service->tarif_harga;



        // Calculate total amount with extra add-ons

        $selectedFeatures = $request->input('selected_features', []);

        if (!empty($selectedFeatures)) {

            $extraCost = ServiceDetail::whereIn('id', $selectedFeatures)->sum('tarif_harga');

            $totalAmount += $extraCost;

        }



        // Get or Create Client Profile Record for Foreign Key Integrity

        $clientProfile = RoleClient::firstOrCreate(

            ['id_user' => auth()->id()],

            [

                'nama' => auth()->user()->nama,

                'nomor_telepon' => auth()->user()->nomor_telepon ?? null,

                'alamat' => $request->input('lokasi_acara'),

            ]

        );



        $paymentType = $request->input('payment_type', 'full');

        if (!in_array($paymentType, ['full', 'dp'])) {

            $paymentType = 'full';

        }



        $jumlahDp = 0;

        $initialPaymentAmount = $totalAmount;

        $paymentExternalId = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(6));



        if ($paymentType === 'dp') {

            $jumlahDp = round($totalAmount * 0.30); // Uang Muka 30%

            $initialPaymentAmount = $jumlahDp;

            $paymentExternalId = 'INV-DP-' . date('Ymd') . '-' . strtoupper(Str::random(6));

        }



        // 1. Create Contract (Matches enum: draft, approved, pending_payment, active, completed, cancelled)

        $contract = Contract::create([

            'id_client' => $clientProfile->id,

            'id_photographer' => $request->input('photographer_id'),

            'jumlah' => $totalAmount,

            'payment_type' => $paymentType,

            'jumlah_dp' => $jumlahDp,

            'is_validated_photographer' => false,

            'is_validated_client' => true,

            'expired_at' => now()->addDays(1),

            'status_contract' => 'pending_payment',

        ]);



        // 2. Create Contract Booking Detail (Matches enum: pending, confirmed, completed)

        ContractBooking::create([

            'id_contract' => $contract->id,

            'id_service' => $service->id,

            'booking_date' => $request->input('tanggal_sesi'),

            'jam_mulai' => $bookingStart . ':00',

            'jam_selesai' => $bookingEnd . ':00',

            'lokasi' => $request->input('lokasi_acara'),

            'province_code' => $request->input('province_code'),

            'city_code' => $request->input('city_code'),

            'catatan_khusus' => $request->input('catatan_khusus'),

            'durasi' => $durasi,

            'status_booking' => 'pending',

        ]);



        // 3. Create Payment Invoice (Matches enum: transfer, payment gateway)

        Payment::create([

            'id_contract' => $contract->id,

            'payment_amount' => $initialPaymentAmount,

            'payment_status' => 'pending',

            'contract_payment_type' => $paymentType,

            'payment_type' => 'transfer',

            'external_id' => $paymentExternalId,

        ]);



        // Record Activity Log

        \App\Models\ActivityLog::record(

            'booking_created',

            "Klien {$clientProfile->nama} membuat reservasi sesi KTRK-" . str_pad($contract->id, 5, '0', STR_PAD_LEFT) . " untuk layanan '{$service->nama_layanan}' (" . ($paymentType === 'dp' ? 'Uang Muka DP 30%' : 'Bayar Penuh') . ")",

            $contract,

            ['total_amount' => $totalAmount, 'payment_type' => $paymentType, 'jumlah_dp' => $jumlahDp, 'tanggal_sesi' => $request->input('tanggal_sesi')]

        );



        return redirect()->route('booking.invoice', $contract->id)->with('success', 'Reservasi sesi foto berhasil diajukan! Silakan lakukan pembayaran invoice.');

    }



    /**

     * Display Payment Invoice Summary for a Booking.

     */

    public function showInvoice(Request $request, $id)

    {

        $clientProfile = RoleClient::where('id_user', auth()->id())->first();

        

        $contract = Contract::with([

            'client.user',

            'photographer.user',

            'photographer.province',

            'photographer.city',

            'bookingDetail.service',

            'bookingDetail.province',

            'bookingDetail.city',

            'payments'

        ])

        ->when($clientProfile, function ($q) use ($clientProfile) {

            $q->where('id_client', $clientProfile->id);

        })

        ->findOrFail($id);



        $dpPayment = $contract->payments->where('contract_payment_type', 'dp')->first();

        $fullPayment = $contract->payments->where('contract_payment_type', 'full')->first();



        // Jika kontrak DP dan DP sudah lunas, pastikan ada record pembayaran pelunasan jika belum ada

        if ($contract->payment_type === 'dp' && $dpPayment && $dpPayment->payment_status === 'completed' && !$fullPayment) {

            $sisaPelunasan = max(0, $contract->jumlah - $contract->jumlah_dp);

            $fullPayment = Payment::create([

                'id_contract' => $contract->id,

                'payment_amount' => $sisaPelunasan,

                'payment_status' => 'pending',

                'contract_payment_type' => 'full',

                'payment_type' => 'transfer',

                'external_id' => 'INV-LNS-' . date('Ymd') . '-' . strtoupper(Str::random(6)),

            ]);

            $contract->load('payments');

        }



        // Tentukan pembayaran yang aktif untuk ditampilkan pada aksi bayar

        $requestedPaymentId = $request->query('payment_id');

        $payment = null;

        if ($requestedPaymentId) {

            $payment = $contract->payments->where('id', $requestedPaymentId)->first();

        }



        if (!$payment) {

            // Prioritaskan pembayaran yang masih pending

            $payment = $contract->payments->where('payment_status', 'pending')->first();

            if (!$payment) {

                $payment = $fullPayment ?: ($dpPayment ?: $contract->payments->first());

            }

        }



        if (!$payment && $contract->payments->isEmpty()) {

            $payment = Payment::create([

                'id_contract' => $contract->id,

                'payment_amount' => $contract->jumlah,

                'payment_status' => 'pending',

                'contract_payment_type' => $contract->payment_type ?: 'full',

                'payment_type' => 'transfer',

                'external_id' => 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(6)),

            ]);

            $contract->load('payments');

        }



        return view('public.booking.invoice', compact('contract', 'payment', 'dpPayment', 'fullPayment'));

    }



    /**

     * Konfirmasi Pembayaran Invoice Booking oleh Klien (Simulasi QRIS / Transfer Manual).

     */

    public function confirmPayment(Request $request, $id)

    {

        $clientProfile = RoleClient::where('id_user', auth()->id())->first();



        $contract = Contract::with([

            'client.user',

            'photographer.user',

            'payments'

        ])

        ->when($clientProfile, function ($q) use ($clientProfile) {

            $q->where('id_client', $clientProfile->id);

        })

        ->findOrFail($id);



        $paymentId = $request->input('payment_id');

        $payment = $paymentId 

            ? $contract->payments->where('id', $paymentId)->first()

            : $contract->payments->where('payment_status', 'pending')->first();



        if (!$payment) {

            $payment = $contract->payments->first();

        }



        $buktiUrl = null;

        if ($request->hasFile('bukti_transfer')) {

            $request->validate([

                'bukti_transfer' => 'image|max:10240',

            ]);

            $path = $request->file('bukti_transfer')->store('booking_payment_proofs', 'public');

            $buktiUrl = asset('storage/' . $path);



            if ($payment) {

                $payment->update([

                    'payment_status' => 'pending',

                    'bukti_transfer' => $buktiUrl,

                    'payment_type' => 'transfer',

                ]);

            }



            $contract->update([

                'status_contract' => 'pending_payment',

            ]);



            $paidAmount = $payment ? $payment->payment_amount : $contract->jumlah;

            $formattedPaidAmount = 'Rp ' . number_format($paidAmount, 0, ',', '.');

            $code = 'KTRK-' . str_pad($contract->id, 5, '0', STR_PAD_LEFT);



            $admins = \App\Models\User::whereIn('role', ['admin', 'super_admin'])->get();

            foreach ($admins as $adm) {

                \App\Models\Notification::send(

                    $adm->id,

                    'Bukti Transfer Baru Diunggah',

                    "Klien {$contract->client->nama} telah mengunggah bukti transfer untuk sesi {$code} ({$formattedPaidAmount}). Mohon segera diverifikasi di panel admin.",

                    'payment_proof_uploaded',

                    url('/admin/transactions')

                );

            }



            \App\Models\ActivityLog::record(

                'payment_proof_uploaded',

                "Klien {$contract->client->nama} mengunggah bukti transfer untuk sesi {$code} ({$formattedPaidAmount})",

                $contract,

                ['bukti_transfer' => $buktiUrl]

            );



            return redirect()->route('booking.invoice', $contract->id)->with('success', 'Bukti transfer berhasil diunggah! Pembayaran Anda sedang diverifikasi oleh Tim Admin.');

        }



        // Update Payment Record

        if ($payment) {

            $payment->update([

                'payment_status' => 'completed',

                'bukti_transfer' => $buktiUrl ?: ($payment->bukti_transfer ?: 'SIMULASI_ESCROW_TRANSFER_OK'),

                'payment_type' => $request->input('payment_method', 'transfer'),

            ]);

        }



        // Identifikasi jenis pembayaran

        $isDpPayment = $payment && $payment->contract_payment_type === 'dp';

        $isPelunasanPayment = $payment && $payment->contract_payment_type === 'full' && $contract->payment_type === 'dp';



        // Update Contract & Escrow

        $contract->update([

            'status_contract' => 'active',

            'status_payout' => 'holding',

            'is_validated_client' => true,

        ]);



        $paidAmount = $payment ? $payment->payment_amount : $contract->jumlah;

        $formattedPaidAmount = 'Rp ' . number_format($paidAmount, 0, ',', '.');

        $code = 'KTRK-' . str_pad($contract->id, 5, '0', STR_PAD_LEFT);



        $notifTitle = 'Pembayaran Sesi Foto Masuk ke Escrow';

        $notifDesc = "Pembayaran sebesar {$formattedPaidAmount} untuk sesi {$code} telah diterima di Rekening Bersama Escrow LensMatch.";



        if ($isDpPayment) {

            $notifTitle = 'Pembayaran DP Booking Diterima';

            $notifDesc = "Pembayaran Uang Muka (DP 30%) sebesar {$formattedPaidAmount} untuk sesi {$code} telah diterima di Escrow. Jadwal pemotretan aktif.";

        } elseif ($isPelunasanPayment) {

            $notifTitle = 'Pelunasan Sisa Sesi Foto Berhasil';

            $notifDesc = "Pembayaran pelunasan sisa tagihan sebesar {$formattedPaidAmount} untuk sesi {$code} telah diterima di Escrow. Sesi kini lunas 100%.";

        }



        // Notifikasi ke Mitra Fotografer

        if ($contract->photographer && $contract->photographer->id_user) {

            \App\Models\Notification::send(

                $contract->photographer->id_user,

                $notifTitle,

                $notifDesc,

                'booking_paid',

                url('/photographer/bookings')

            );

        }



        // Notifikasi ke Klien

        if ($contract->client && $contract->client->id_user) {

            \App\Models\Notification::send(

                $contract->client->id_user,

                $notifTitle,

                $notifDesc . " Dana Anda tersimpan aman dengan garansi Escrow.",

                'payment_success',

                url('/pembayaran/' . $contract->id)

            );

        }



        // Record Activity Log

        \App\Models\ActivityLog::record(

            'payment_confirmed',

            $notifDesc,

            $contract,

            ['amount' => $paidAmount, 'payment_type' => $payment ? $payment->contract_payment_type : 'full']

        );



        return redirect()->route('booking.invoice', $contract->id)->with('success', 'Pembayaran berhasil dikonfirmasi! Dana disimpan aman di Rekening Bersama Escrow LensMatch.');

    }



    /**

     * Tampilkan Dokumen Invoice Resmi Print/PDF Ready.

     */

    public function printInvoice(Request $request, $id)

    {

        $contract = Contract::with([

            'client.user',

            'photographer.user',

            'photographer.province',

            'photographer.city',

            'bookingDetail.service',

            'bookingDetail.province',

            'bookingDetail.city',

            'payments'

        ])->findOrFail($id);



        $dpPayment = $contract->payments->where('contract_payment_type', 'dp')->first();

        $fullPayment = $contract->payments->where('contract_payment_type', 'full')->first();

        $activePayment = $contract->payments->last();



        return view('public.booking.invoice-print', compact('contract', 'dpPayment', 'fullPayment', 'activePayment'));

    }

}