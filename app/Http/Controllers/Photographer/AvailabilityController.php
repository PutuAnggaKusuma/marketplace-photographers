<?php

namespace App\Http\Controllers\Photographer;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\PhotographerAvailability;
use App\Models\RolePhotographer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AvailabilityController extends Controller
{
    /**
     * Tampilkan Halaman Form Blokir Tanggal / Libur Jadwal.
     */
    public function create()
    {
        $user = Auth::user();
        $photographer = RolePhotographer::where('id_user', $user->id)->first();
        if (!$photographer) $photographer = RolePhotographer::first();

        return view('photographer.availability.create', compact('photographer'));
    }

    /**
     * Tampilkan Halaman Jadwal & Kalender Ketersediaan Fotografer.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $photographer = RolePhotographer::where('id_user', $user->id)->first();
        if (!$photographer) {
            $photographer = RolePhotographer::first();
        }

        $search = $request->query('search', '');
        $sort = $request->query('sort', 'date_asc');
        $query = PhotographerAvailability::where('id_photographer', $photographer->id);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('keterangan', 'like', "%{$search}%")
                  ->orWhere('tanggal', 'like', "%{$search}%");
            });
        }

        if ($sort === 'date_desc') {
            $query->orderBy('tanggal', 'desc');
        } elseif ($sort === 'latest') {
            $query->orderBy('created_at', 'desc');
        } elseif ($sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('tanggal', 'asc');
        }

        $perPage = (int) $request->query('per_page', 10);
        if (!in_array($perPage, [10, 20, 50, 100])) $perPage = 10;
        $availabilities = $query->paginate($perPage)->withQueryString();

        // Metrics & Booked Dates
        $allAvailabilities = PhotographerAvailability::where('id_photographer', $photographer->id)->get();
        $totalBlocked = $allAvailabilities->count();
        
        $activeBookings = Contract::where('id_photographer', $photographer->id)
            ->whereIn('status_contract', ['active', 'approved', 'pending_payment'])
            ->with(['bookingDetail', 'client.user'])
            ->get();
        $totalActiveBookings = $activeBookings->count();

        $nearestBlocked = $allAvailabilities->where('tanggal', '>=', date('Y-m-d'))->sortBy('tanggal')->first();

        // Calendar event lists
        $calendarEvents = [];
        foreach ($allAvailabilities as $avail) {
            $calendarEvents[] = [
                'title' => 'Blokir: ' . ($avail->keterangan ?: 'Manual'),
                'date' => $avail->tanggal,
                'type' => 'blocked'
            ];
        }
        foreach ($activeBookings as $b) {
            if ($b->bookingDetail) {
                $calendarEvents[] = [
                    'title' => 'Booking: ' . ($b->client->nama ?? 'Klien'),
                    'date' => $b->bookingDetail->booking_date,
                    'type' => 'booked'
                ];
            }
        }

        return view('photographer.availability.index', compact(
            'photographer',
            'availabilities',
            'search',
            'sort',
            'totalBlocked',
            'totalActiveBookings',
            'nearestBlocked',
            'calendarEvents'
        ));
    }

    /**
     * Blokir Tanggal / Jadwal Secara Manual.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $photographer = RolePhotographer::where('id_user', $user->id)->first();
        if (!$photographer) {
            $photographer = RolePhotographer::first();
        }

        $request->validate([
            'tanggal' => 'required|date',
            'jam_mulai' => 'nullable|string',
            'jam_selesai' => 'nullable|string',
            'keterangan' => 'nullable|string|max:500',
        ], [
            'tanggal.required' => 'Tanggal jadwal wajib diisi.',
            'tanggal.date' => 'Format tanggal tidak valid.',
        ]);

        PhotographerAvailability::create([
            'id_photographer' => $photographer->id,
            'tanggal' => $request->tanggal,
            'jam_mulai' => $request->jam_mulai ?: '00:00',
            'jam_selesai' => $request->jam_selesai ?: '23:59',
            'status' => 'blocked',
            'keterangan' => trim($request->keterangan) ?: 'Jadwal ditutup manual oleh fotografer',
        ]);

        return redirect()->route('photographer.availability')->with('success', 'Jadwal berhasil diblokir manual!');
    }

    /**
     * Buka Blokir Jadwal.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $photographer = RolePhotographer::where('id_user', $user->id)->first();
        if (!$photographer) {
            $photographer = RolePhotographer::first();
        }

        $avail = PhotographerAvailability::where('id_photographer', $photographer->id)->findOrFail($id);
        $avail->delete();

        return redirect()->route('photographer.availability')->with('success', 'Blokir jadwal berhasil dibuka!');
    }
}