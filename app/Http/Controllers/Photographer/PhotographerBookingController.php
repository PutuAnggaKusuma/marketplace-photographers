<?php

namespace App\Http\Controllers\Photographer;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\RolePhotographer;
use Illuminate\Http\Request;

class PhotographerBookingController extends Controller
{
    /**
     * Display Photographer Incoming Booking Orders.
     */
    public function index(Request $request)
    {
        $photographer = RolePhotographer::where('id_user', auth()->id())->first();

        // If no photographer profile exists yet, pick first photographer for demo
        if (!$photographer) {
            $photographer = RolePhotographer::first();
        }

        $filterStatus = $request->input('status', 'all');

        $query = Contract::with([
            'client.user',
            'bookingDetail.service',
            'bookingDetail.province',
            'bookingDetail.city',
            'payments'
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

        $contracts = $query->paginate(10);

        // Stats summary count
        $stats = [
            'all' => Contract::where('id_photographer', $photographer->id)->count(),
            'pending' => Contract::where('id_photographer', $photographer->id)->whereHas('bookingDetail', fn($q) => $q->where('status_booking', 'pending'))->count(),
            'confirmed' => Contract::where('id_photographer', $photographer->id)->whereHas('bookingDetail', fn($q) => $q->where('status_booking', 'confirmed'))->count(),
            'completed' => Contract::where('id_photographer', $photographer->id)->whereHas('bookingDetail', fn($q) => $q->where('status_booking', 'completed'))->count(),
        ];

        return view('photographer.bookings.index', compact('contracts', 'stats', 'filterStatus', 'photographer'));
    }

    /**
     * Update Booking Order Status (Confirm, Cancel, Complete).
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:confirmed,cancelled,completed',
        ]);

        $photographer = RolePhotographer::where('id_user', auth()->id())->first();
        if (!$photographer) {
            $photographer = RolePhotographer::first();
        }

        $contract = Contract::where('id_photographer', $photographer->id)->findOrFail($id);
        $status = $request->input('status');

        if ($status === 'confirmed') {
            $contract->bookingDetail()->update(['status_booking' => 'confirmed']);
            $contract->update(['status_contract' => 'active']);
            $message = 'Pesanan booking berhasil dikonfirmasi! Klien telah diberitahukan.';
        } elseif ($status === 'cancelled') {
            $contract->bookingDetail()->update(['status_booking' => 'cancelled']);
            $contract->update(['status_contract' => 'cancelled']);
            $message = 'Pesanan booking telah ditolak / dibatalkan.';
        } elseif ($status === 'completed') {
            $contract->bookingDetail()->update(['status_booking' => 'completed']);
            $contract->update(['status_contract' => 'completed']);
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

        $photographer = RolePhotographer::where('id_user', auth()->id())->first();
        if (!$photographer) {
            $photographer = RolePhotographer::first();
        }

        $contract = Contract::where('id_photographer', $photographer->id)->findOrFail($id);

        $contract->bookingDetail()->update([
            'hasil_foto_url' => $request->input('hasil_foto_url'),
            'catatan_fotografer' => $request->input('catatan_fotografer'),
            'status_booking' => 'completed',
        ]);

        $contract->update(['status_contract' => 'completed']);

        return redirect()->back()->with('success', 'Tautan galeri foto berhasil diunggah & dikirimkan ke Klien!');
    }
}