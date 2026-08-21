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
                ['nama' => auth()->user()->nama ?? 'Client LensMatch']
            );
        }

        $contracts = Contract::with([
            'photographer.user',
            'photographer.province',
            'photographer.city',
            'bookingDetail.service',
            'bookingDetail.province',
            'bookingDetail.city',
            'payments'
        ])
        ->where('id_client', $clientProfile->id)
        ->latest()
        ->paginate(10);

        return view('client.bookings.index', compact('contracts', 'clientProfile'));
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
                ['nama' => auth()->user()->nama ?? 'Client LensMatch']
            );
        }

        $contracts = Contract::with([
            'photographer',
            'bookingDetail.service',
            'payments'
        ])
        ->where('id_client', $clientProfile->id)
        ->latest()
        ->paginate(10);

        return view('client.invoices.index', compact('contracts', 'clientProfile'));
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
                ['nama' => auth()->user()->nama ?? 'Client LensMatch']
            );
        }

        $contracts = Contract::with([
            'photographer.user',
            'bookingDetail.service',
            'payments'
        ])
        ->where('id_client', $clientProfile->id)
        ->whereHas('bookingDetail', function ($q) {
            $q->whereNotNull('hasil_foto_url');
        })
        ->latest()
        ->paginate(10);

        return view('client.galleries.index', compact('contracts', 'clientProfile'));
    }
}