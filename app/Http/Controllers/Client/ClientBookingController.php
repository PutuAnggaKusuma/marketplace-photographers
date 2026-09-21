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

        $query = Contract::with([
            'photographer.user',
            'photographer.province',
            'photographer.city',
            'bookingDetail.service',
            'bookingDetail.province',
            'bookingDetail.city',
            'payments',
            'testimonial'
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

        return view('client.bookings.index', compact('contracts', 'clientProfile', 'filterStatus', 'stats'));
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
        ->paginate((int) ($request->query('per_page') && in_array((int)$request->query('per_page'), [10, 20, 50, 100]) ? $request->query('per_page') : 10))->withQueryString();

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
            'payments',
            'testimonial'
        ])
        ->where('id_client', $clientProfile->id)
        ->whereHas('bookingDetail', function ($q) {
            $q->whereNotNull('hasil_foto_url');
        })
        ->latest()
        ->paginate((int) ($request->query('per_page') && in_array((int)$request->query('per_page'), [10, 20, 50, 100]) ? $request->query('per_page') : 10))->withQueryString();

        return view('client.galleries.index', compact('contracts', 'clientProfile'));
    }
}