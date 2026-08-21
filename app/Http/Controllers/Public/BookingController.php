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

        $serviceId = $request->input('service_id');
        if ($serviceId) {
            $service = PhotographerService::with('details')->where('id_photographer', $photographer->id)->find($serviceId);
        }
        
        if (!isset($service) || !$service) {
            $service = $photographer->services()->with('details')->first();
        }

        $provinces = Province::orderBy('name', 'asc')->get();

        return view('public.booking.create', compact('photographer', 'service', 'provinces'));
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
                'nomor_telepon' => '08123456789',
                'alamat' => $request->input('lokasi_acara'),
            ]
        );

        // 1. Create Contract (Matches enum: draft, approved, pending_payment, active, completed, cancelled)
        $contract = Contract::create([
            'id_client' => $clientProfile->id,
            'id_photographer' => $request->input('photographer_id'),
            'jumlah' => $totalAmount,
            'payment_type' => 'full',
            'jumlah_dp' => 0,
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
            'jam_mulai' => $request->input('jam_mulai'),
            'jam_selesai' => date('H:i', strtotime($request->input('jam_mulai') . ' +4 hours')),
            'lokasi' => $request->input('lokasi_acara'),
            'province_code' => $request->input('province_code'),
            'city_code' => $request->input('city_code'),
            'catatan_khusus' => $request->input('catatan_khusus'),
            'durasi' => 4,
            'status_booking' => 'pending',
        ]);

        // 3. Create Payment Invoice (Matches enum: transfer, payment gateway)
        Payment::create([
            'id_contract' => $contract->id,
            'payment_amount' => $totalAmount,
            'payment_status' => 'pending',
            'contract_payment_type' => 'full',
            'payment_type' => 'transfer',
            'external_id' => 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(6)),
        ]);

        return redirect()->route('booking.invoice', $contract->id)->with('success', 'Reservasi sesi foto berhasil diajukan! Silakan lakukan pembayaran invoice.');
    }

    /**
     * Display Payment Invoice Summary for a Booking.
     */
    public function showInvoice($id)
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

        $payment = $contract->payments->first();

        return view('public.booking.invoice', compact('contract', 'payment'));
    }
}