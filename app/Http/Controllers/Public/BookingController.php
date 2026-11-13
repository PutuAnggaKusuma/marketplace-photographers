<?php



namespace App\Http\Controllers\Public;



use App\Http\Controllers\Controller;
use App\Models\ChatBooking;
use App\Models\ChatMessage;
use App\Models\Contract;
use App\Models\ContractBooking;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\PhotographerService;
use App\Models\RoleClient;
use App\Models\RolePhotographer;
use App\Services\BookingScheduleService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            $photographer = RolePhotographer::published()->with(['services.details', 'user', 'province', 'city'])->first();
            if (!$photographer) {
                return redirect()->route('public.photographers.katalog')
                    ->with('warning', 'Belum ada fotografer yang siap menerima pemesanan.');
            }
        } else {
            $photographer = RolePhotographer::with(['services.details', 'user', 'province', 'city'])->findOrFail($photographerId);
            if (!$photographer->isPublishedToCatalog()) {
                abort(404, 'Etalase fotografer ini belum aktif untuk pemesanan.');
            }
        }



        $allServices = $photographer->services()->with('details')->get();



        $serviceId = $request->input('service_id', old('service_id'));

        $service = null;

        if ($serviceId) {

            $service = $allServices->firstWhere('id', $serviceId);

        }



        $provinces = Province::orderBy('name', 'asc')->get();



        $preselectedDate = $request->input('tanggal', '');



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



        // Full-day unavailable dates (manual full-day blocks + multi-day custom bookings)
        $blockedDates = BookingScheduleService::getFullDayUnavailableDates($photographer->id, date('Y-m-d'));

        return view('public.booking.create', compact(
            'photographer',
            'service',
            'allServices',
            'provinces',
            'preselectedDate',
            'blockedDates',
            'workingHours'
        ));
    }

    /**
     * Endpoint JSON untuk mengambil matrix jam mulai & jam selesai bulat
     * sesuai paket (spare time + bypass 500m), tanggal, dan koordinat venue acara.
     */
    public function availableSlots(Request $request)
    {
        $request->validate([
            'photographer_id' => 'required|exists:role_photographers,id',
            'date' => 'required|date_format:Y-m-d',
            'service_id' => 'nullable|exists:photographer_services,id',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        $photographer = RolePhotographer::findOrFail($request->input('photographer_id'));
        if (!$photographer->isPublishedToCatalog()) {
            return response()->json(['message' => 'Etalase fotografer tidak aktif.'], 404);
        }

        $service = $request->filled('service_id')
            ? PhotographerService::where('id_photographer', $photographer->id)->find($request->input('service_id'))
            : null;

        $targetOptions = [
            'spare_before_minutes' => $service ? $service->getEffectiveSpareBeforeMinutes() : 0,
            'spare_after_minutes' => $service ? $service->getEffectiveSpareAfterMinutes() : 0,
            'allow_same_location_bypass' => $service ? (bool) $service->allow_same_location_bypass : false,
            'latitude' => $request->filled('latitude') ? (float) $request->input('latitude') : null,
            'longitude' => $request->filled('longitude') ? (float) $request->input('longitude') : null,
        ];

        $matrix = BookingScheduleService::getAvailableTimeMatrix(
            $photographer->id,
            $request->input('date'),
            $targetOptions
        );

        return response()->json($matrix);
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
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i',
            'lokasi_acara' => 'required|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'province_code' => 'nullable|string',
            'city_code' => 'nullable|string',
            'catatan_khusus' => 'nullable|string',
        ], [
            'jam_mulai.required' => 'Jam mulai sesi wajib dipilih.',
            'jam_mulai.date_format' => 'Format jam mulai tidak valid.',
            'jam_selesai.required' => 'Jam selesai sesi wajib dipilih.',
            'jam_selesai.date_format' => 'Format jam selesai tidak valid.',
            'lokasi_acara.required' => 'Alamat lokasi acara wajib diisi.',
        ]);

        $tanggalSesi = $request->input('tanggal_sesi');
        $photographerId = (int) $request->input('photographer_id');

        $targetPhotographer = RolePhotographer::findOrFail($photographerId);
        if (!$targetPhotographer->isPublishedToCatalog()) {
            return redirect()->back()->withInput()->with('error', 'Etalase fotografer belum aktif untuk menerima pemesanan.');
        }

        $service = PhotographerService::with('details')
            ->where('id_photographer', $photographerId)
            ->findOrFail($request->input('service_id'));

        $startMin = BookingScheduleService::parseTimeToMinutes($request->input('jam_mulai'));
        $endMin = BookingScheduleService::parseEndTimeToMinutes($request->input('jam_selesai'));

        if ($startMin === null || $endMin === null) {
            return redirect()->back()->withInput()->with('error', 'Format jam mulai atau jam selesai tidak valid.');
        }

        $lat = $request->filled('latitude') ? (float) $request->input('latitude') : null;
        $lng = $request->filled('longitude') ? (float) $request->input('longitude') : null;

        $targetOptions = [
            'spare_before_minutes' => $service->getEffectiveSpareBeforeMinutes(),
            'spare_after_minutes' => $service->getEffectiveSpareAfterMinutes(),
            'allow_same_location_bypass' => (bool) $service->allow_same_location_bypass,
            'latitude' => $lat,
            'longitude' => $lng,
        ];

        $conflictError = BookingScheduleService::validateRegularBookingSlot(
            $photographerId,
            $tanggalSesi,
            $startMin,
            $endMin,
            $targetOptions
        );

        if ($conflictError !== null) {
            return redirect()->back()->withInput()->with('error', $conflictError);
        }

        $durasi = (int) (($endMin - $startMin) / 60);
        if ($durasi < 1) {
            return redirect()->back()->withInput()->with('error', 'Durasi pemesanan minimal 1 jam.');
        }

        $bookingStart = BookingScheduleService::formatMinutesToTime($startMin);
        $bookingEnd = BookingScheduleService::formatMinutesToTime($endMin);

        $totalAmount = (float) $service->tarif_harga * $durasi;
        $feeMarketplace = round($totalAmount * 0.10);
        $payoutAmount = max(0, $totalAmount - $feeMarketplace);

        $clientProfile = RoleClient::firstOrCreate(
            ['id_user' => auth()->id()],
            [
                'nama' => auth()->user()->nama,
                'nomor_telepon' => auth()->user()->nomor_telepon ?? null,
                'alamat' => $request->input('lokasi_acara'),
            ]
        );

        $paymentType = $request->input('payment_type', 'full');
        if (!in_array($paymentType, ['full', 'dp'], true)) {
            $paymentType = 'full';
        }

        $jumlahDp = 0;
        $initialPaymentAmount = $totalAmount;
        $paymentExternalId = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(6));

        if ($paymentType === 'dp') {
            $jumlahDp = round($totalAmount * 0.30);
            $initialPaymentAmount = $jumlahDp;
            $paymentExternalId = 'INV-DP-' . date('Ymd') . '-' . strtoupper(Str::random(6));
        }

        $contract = Contract::create([
            'id_client' => $clientProfile->id,
            'id_photographer' => $photographerId,
            'jumlah' => $totalAmount,
            'payment_type' => $paymentType,
            'jumlah_dp' => $jumlahDp,
            'fee_marketplace' => $feeMarketplace,
            'payout_amount' => $payoutAmount,
            'is_validated_photographer' => false,
            'is_validated_client' => true,
            'expired_at' => now()->addDays(1),
            'status_contract' => 'pending_payment',
        ]);

        ContractBooking::create([
            'id_contract' => $contract->id,
            'id_service' => $service->id,
            'booking_date' => $tanggalSesi,
            'booking_end_date' => $tanggalSesi,
            'jam_mulai' => BookingScheduleService::formatMinutesToDbTime($startMin),
            'jam_selesai' => BookingScheduleService::formatMinutesToDbTime($endMin),
            'lokasi' => $request->input('lokasi_acara'),
            'latitude' => $lat,
            'longitude' => $lng,
            'province_code' => $request->input('province_code'),
            'city_code' => $request->input('city_code'),
            'catatan_khusus' => $request->input('catatan_khusus'),
            'durasi' => $durasi,
            'spare_before_minutes' => $service->getEffectiveSpareBeforeMinutes(),
            'spare_after_minutes' => $service->getEffectiveSpareAfterMinutes(),
            'allow_same_location_bypass' => (bool) $service->allow_same_location_bypass,
            'status_booking' => 'pending',
        ]);

        Payment::create([
            'id_contract' => $contract->id,
            'payment_amount' => $initialPaymentAmount,
            'payment_status' => 'pending',
            'contract_payment_type' => $paymentType,
            'payment_type' => 'transfer',
            'external_id' => $paymentExternalId,
        ]);

        \App\Models\ActivityLog::record(
            'booking_created',
            "Klien {$clientProfile->nama} membuat reservasi sesi KTRK-" . str_pad($contract->id, 5, '0', STR_PAD_LEFT) . " ({$bookingStart}-{$bookingEnd} WIB, {$durasi} jam) untuk layanan '{$service->nama_layanan}' (" . ($paymentType === 'dp' ? 'DP 30%' : 'Bayar Penuh') . ")",
            $contract,
            [
                'total_amount' => $totalAmount,
                'durasi' => $durasi,
                'payment_type' => $paymentType,
                'jumlah_dp' => $jumlahDp,
                'tanggal_sesi' => $tanggalSesi,
            ]
        );

        return redirect()->route('booking.invoice', $contract->id)
            ->with('success', 'Reservasi sesi foto berhasil diajukan. Silakan lanjutkan pembayaran invoice.');
    }

    /**
     * Kirim pengajuan Booking Custom (multi-hari berurutan atau jam khusus) sebagai pesan pembuka Chat.
     */
    public function sendCustomPlanRequest(Request $request)
    {
        $request->validate([
            'photographer_id' => 'required|exists:role_photographers,id',
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'jam_mulai' => 'nullable|date_format:H:i',
            'jam_selesai' => 'nullable|date_format:H:i',
            'lokasi' => 'required|string|max:500',
            'kebutuhan' => 'required|string|max:2000',
        ], [
            'tanggal_mulai.required' => 'Tanggal mulai acara wajib diisi.',
            'tanggal_selesai.required' => 'Tanggal selesai acara wajib diisi.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',
            'lokasi.required' => 'Lokasi acara wajib diisi.',
            'kebutuhan.required' => 'Rincian kebutuhan acara wajib diisi.',
        ]);

        $photographer = RolePhotographer::findOrFail($request->input('photographer_id'));
        if (!$photographer->isPublishedToCatalog()) {
            return redirect()->back()->withInput()->with('error', 'Etalase fotografer belum aktif menerima pemesanan.');
        }

        if (
            $request->input('tanggal_mulai') === $request->input('tanggal_selesai')
            && $request->filled('jam_mulai')
            && $request->filled('jam_selesai')
            && $request->input('jam_selesai') <= $request->input('jam_mulai')
        ) {
            return redirect()->back()->withInput()->with('error', 'Jam selesai harus lebih akhir daripada jam mulai.');
        }

        $clientProfile = RoleClient::firstOrCreate(
            ['id_user' => auth()->id()],
            [
                'nama' => auth()->user()->nama,
                'nomor_telepon' => auth()->user()->nomor_telepon ?? null,
                'alamat' => $request->input('lokasi'),
            ]
        );

        $thread = ChatBooking::firstOrCreate([
            'id_client' => $clientProfile->id,
            'id_photographer' => $photographer->id,
        ]);

        $startCarbon = Carbon::parse($request->input('tanggal_mulai'));
        $endCarbon = Carbon::parse($request->input('tanggal_selesai'));
        $dayCount = (int) $startCarbon->diffInDays($endCarbon) + 1;

        $dateLine = $startCarbon->isSameDay($endCarbon)
            ? $startCarbon->translatedFormat('d M Y')
            : $startCarbon->translatedFormat('d M Y') . ' s/d ' . $endCarbon->translatedFormat('d M Y') . " ({$dayCount} hari)";

        $timeLine = ($request->filled('jam_mulai') && $request->filled('jam_selesai'))
            ? $request->input('jam_mulai') . ' - ' . $request->input('jam_selesai') . ' WIB'
            : ($request->filled('jam_mulai')
                ? 'Mulai ' . $request->input('jam_mulai') . ' WIB'
                : 'Menyesuaikan kesepakatan');

        $openingMessage = "Permintaan Booking Custom\n"
            . "Tanggal: {$dateLine}\n"
            . "Perkiraan Jam: {$timeLine}\n"
            . "Lokasi: " . trim($request->input('lokasi')) . "\n"
            . "Kebutuhan: " . trim($request->input('kebutuhan'));

        ChatMessage::create([
            'id_chat_booking' => $thread->id,
            'id_sender' => auth()->id(),
            'message' => $openingMessage,
            'is_read' => false,
        ]);

        $thread->touch();

        if ($photographer->id_user) {
            Notification::send(
                $photographer->id_user,
                'Permintaan Booking Custom',
                "Klien {$clientProfile->nama} mengirimkan rencana Booking Custom ({$dateLine}) melalui chat.",
                'custom_offer_request',
                route('photographer.chat', ['thread' => $thread->id])
            );
        }

        return redirect()->route('client.chat', ['thread' => $thread->id])
            ->with('success', 'Permintaan Booking Custom terkirim ke ruang chat fotografer.');
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

                'bukti_transfer' => 'image|mimes:jpeg,png,jpg,webp|max:10240',

            ], [

                'bukti_transfer.image' => 'Bukti transfer harus berupa gambar.',

                'bukti_transfer.mimes' => 'Format bukti transfer harus JPG, PNG, atau WEBP.',

                'bukti_transfer.max' => 'Ukuran file bukti transfer maksimal 10 MB.',

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