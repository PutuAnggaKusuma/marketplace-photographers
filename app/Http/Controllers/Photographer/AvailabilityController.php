<?php

namespace App\Http\Controllers\Photographer;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\PhotographerAvailability;
use App\Models\PhotographerWorkingHour;
use App\Models\RolePhotographer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AvailabilityController extends Controller
{
    /**
     * Tampilkan Halaman Form Blokir Tanggal / Libur Jadwal (Date Overrides).
     */
    public function create()
    {
        $user = Auth::user();
        $photographer = RolePhotographer::firstOrCreate(['id_user' => $user->id], ['nama' => $user->nama]);

        return view('photographer.availability.create', compact('photographer'));
    }

    /**
     * Tampilkan Halaman Jadwal, Jam Kerja & Kalender Ketersediaan Fotografer.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $photographer = RolePhotographer::firstOrCreate(['id_user' => $user->id], ['nama' => $user->nama]);

        // Pastikan konfigurasi default 7 hari kerja tersedia
        PhotographerWorkingHour::initializeDefaults($photographer->id);
        $workingHours = $photographer->workingHours;

        $tab = $request->query('tab', 'weekly'); // 'weekly' or 'overrides'
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
        $nextBooking = $activeBookings->filter(function ($b) {
            return $b->bookingDetail && $b->bookingDetail->booking_date >= date('Y-m-d');
        })->sortBy(function ($b) {
            return $b->bookingDetail->booking_date;
        })->first();
        $nextBookingDate = $nextBooking ? date('d M Y', strtotime($nextBooking->bookingDetail->booking_date)) : 'Belum Ada';

        // Calendar event lists
        $calendarEvents = [];
        foreach ($allAvailabilities as $avail) {
            $jamInfo = ($avail->jam_mulai && $avail->jam_selesai && ($avail->jam_mulai != '00:00:00' || $avail->jam_selesai != '23:59:59')) 
                ? ' (' . substr($avail->jam_mulai, 0, 5) . '-' . substr($avail->jam_selesai, 0, 5) . ')' 
                : ' (Libur Penuh)';
            $calendarEvents[] = [
                'title' => 'Blokir: ' . ($avail->keterangan ?: 'Manual') . $jamInfo,
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
            'workingHours',
            'availabilities',
            'tab',
            'search',
            'sort',
            'totalBlocked',
            'totalActiveBookings',
            'nearestBlocked',
            'nextBookingDate',
            'calendarEvents'
        ));
    }

    /**
     * Perbarui Template Jam Kerja Mingguan (Weekly Recurring Hours) dengan Zero-Break Guard.
     */
    public function updateWorkingHours(Request $request)
    {
        $user = Auth::user();
        $photographer = RolePhotographer::firstOrCreate(['id_user' => $user->id], ['nama' => $user->nama]);

        $daysInput = $request->input('days', []);

        // 0. Validasi Jam Kerja: Jam tutup harus lebih akhir daripada jam buka
        foreach ($daysInput as $dow => $cfg) {
            if (!empty($cfg['is_open']) && empty($cfg['is_24_hours'])) {
                $bukaVal = substr($cfg['jam_buka'] ?? '08:00', 0, 5);
                $tutupVal = substr($cfg['jam_tutup'] ?? '17:00', 0, 5);
                if ($bukaVal >= $tutupVal) {
                    $dayNameVal = $cfg['day_name'] ?? ('Hari ke-' . $dow);
                    return redirect()->back()->withInput()->with('error', "Format jam kerja {$dayNameVal} tidak valid: Jam tutup ({$tutupVal} WIB) harus lebih besar/akhir daripada jam buka ({$bukaVal} WIB).");
                }
            }
        }

        // 1. Zero-Break Guard: Cek apakah ada booking aktif mendatang yang terbentur oleh perubahan jam operasional baru
        $upcomingContracts = Contract::where('id_photographer', $photographer->id)
            ->whereIn('status_contract', ['active', 'approved', 'pending_payment'])
            ->whereHas('bookingDetail', function ($q) {
                $q->where('booking_date', '>=', date('Y-m-d'));
            })
            ->with(['bookingDetail', 'client'])
            ->get();

        foreach ($upcomingContracts as $c) {
            $bDate = $c->bookingDetail->booking_date;
            $dow = (int) Carbon::parse($bDate)->dayOfWeekIso; // 1 = Senin, ..., 7 = Minggu
            $bJamMulai = substr($c->bookingDetail->jam_mulai, 0, 5);
            $code = 'KTRK-' . str_pad($c->id, 5, '0', STR_PAD_LEFT);
            $clientName = $c->client->nama ?? 'Klien';

            $newDayConfig = $daysInput[$dow] ?? null;
            $dayName = $newDayConfig['day_name'] ?? ('Hari ke-' . $dow);

            // A. Jika fotografer mematikan hari operasional yang ada pesanan aktifnya
            if (!$newDayConfig || empty($newDayConfig['is_open'])) {
                return redirect()->back()->withInput()->with('error', "Gagal memperbarui jam kerja: Terdapat pesanan aktif ({$code}) dari {$clientName} pada hari {$dayName} (" . date('d M Y', strtotime($bDate)) . "). Anda harus mengajukan Jadwal Ulang (Reschedule) terlebih dahulu sebelum dapat menutup operasional pada hari tersebut.");
            }

            // B. Jika fotografer memperpendek jam kerja dan pesanan berada di luar jam operasional baru
            if (empty($newDayConfig['is_24_hours'])) {
                $newBuka = $newDayConfig['jam_buka'] ?? '08:00';
                $newTutup = $newDayConfig['jam_tutup'] ?? '17:00';

                if ($bJamMulai < $newBuka || $bJamMulai > $newTutup) {
                    return redirect()->back()->withInput()->with('error', "Gagal memperbarui jam kerja: Terdapat pesanan aktif ({$code}) pada {$dayName} (" . date('d M Y', strtotime($bDate)) . ") jam {$bJamMulai} WIB yang berada di luar jam kerja operasional baru ({$newBuka} - {$newTutup} WIB). Silakan ajukan Reschedule kepada klien terlebih dahulu.");
                }
            }
        }

        // 2. Simpan konfigurasi jam kerja 7 hari
        foreach ($daysInput as $dow => $cfg) {
            $isOpen = !empty($cfg['is_open']);
            $is24Hours = !empty($cfg['is_24_hours']);
            $jamBuka = $cfg['jam_buka'] ?? '08:00:00';
            $jamTutup = $cfg['jam_tutup'] ?? '17:00:00';

            PhotographerWorkingHour::updateOrCreate(
                [
                    'id_photographer' => $photographer->id,
                    'day_of_week' => (int) $dow,
                ],
                [
                    'day_name' => $cfg['day_name'] ?? 'Hari',
                    'is_open' => $isOpen,
                    'is_24_hours' => $is24Hours,
                    'jam_buka' => $is24Hours ? '00:00:00' : $jamBuka,
                    'jam_tutup' => $is24Hours ? '23:59:59' : $jamTutup,
                ]
            );
        }

        $photographer->update([
            'jadwal_dikonfirmasi' => true,
        ]);

        $downgraded = $photographer->fresh()->syncStorefrontIntegrity();
        $message = 'Jadwal jam kerja mingguan berhasil diperbaharui!';
        if ($downgraded) {
            $message .= ' Etalase dinonaktifkan sementara hingga minimal satu hari operasional dibuka kembali.';
        }

        return redirect()->route('photographer.availability', ['tab' => 'weekly'])->with('success', $message);
    }

    /**
     * Blokir Tanggal / Jam Jadwal Secara Manual (Date Overrides) dengan Zero-Break Guard.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $photographer = RolePhotographer::firstOrCreate(['id_user' => $user->id], ['nama' => $user->nama]);

        $tipeBlokir = $request->input('tipe_blokir', 'all_day');

        if ($tipeBlokir === 'time_slot') {
            $request->validate([
                'tanggal' => 'required|date',
                'jam_mulai' => 'required|string',
                'jam_selesai' => 'required|string',
                'keterangan' => 'nullable|string|max:500',
            ], [
                'tanggal.required' => 'Tanggal jadwal wajib diisi.',
                'jam_mulai.required' => 'Jam mulai penutupan jadwal wajib diisi.',
                'jam_selesai.required' => 'Jam selesai penutupan jadwal wajib diisi.',
            ]);

            $startSlot = substr($request->jam_mulai, 0, 5);
            $endSlot = substr($request->jam_selesai, 0, 5);
            if ($startSlot >= $endSlot) {
                return redirect()->back()->withInput()->with('error', "Jam selesai penutupan jadwal ({$endSlot} WIB) harus lebih akhir daripada jam mulai ({$startSlot} WIB).");
            }

            // Zero-Break Guard: Cek apakah ada pesanan aktif pada rentang jam tersebut
            $conflict = Contract::where('id_photographer', $photographer->id)
                ->whereIn('status_contract', ['active', 'approved', 'pending_payment'])
                ->whereHas('bookingDetail', function ($q) use ($request) {
                    $q->where('booking_date', $request->tanggal)
                      ->where('jam_mulai', '<=', $request->jam_selesai)
                      ->where('jam_selesai', '>=', $request->jam_mulai);
                })
                ->with(['bookingDetail', 'client'])
                ->first();

            if ($conflict) {
                $code = 'KTRK-' . str_pad($conflict->id, 5, '0', STR_PAD_LEFT);
                $clientName = $conflict->client->nama ?? 'Klien';
                $slotTime = substr($conflict->bookingDetail->jam_mulai, 0, 5) . ' - ' . substr($conflict->bookingDetail->jam_selesai, 0, 5);
                return redirect()->back()->withInput()->with('error', "Gagal menutup slot jam: Terdapat pesanan aktif ({$code}) dari klien {$clientName} pada jam {$slotTime} WIB. Anda harus mengajukan Jadwal Ulang (Reschedule) terlebih dahulu sebelum dapat menutup jadwal ini.");
            }

            PhotographerAvailability::create([
                'id_photographer' => $photographer->id,
                'tanggal' => $request->tanggal,
                'jam_mulai' => $request->jam_mulai,
                'jam_selesai' => $request->jam_selesai,
                'status' => 'blocked',
                'keterangan' => trim($request->keterangan) ?: 'Sesi jam ' . $request->jam_mulai . ' - ' . $request->jam_selesai . ' ditutup manual oleh fotografer',
            ]);
        } else {
            // All day block (single date or date range)
            $tanggalMulai = $request->input('tanggal_mulai', $request->input('tanggal', date('Y-m-d')));
            $tanggalSelesai = $request->input('tanggal_selesai', $tanggalMulai);

            $request->validate([
                'tanggal_mulai' => 'required|date',
                'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
                'keterangan' => 'nullable|string|max:500',
            ], [
                'tanggal_mulai.required' => 'Tanggal libur wajib diisi.',
                'tanggal_selesai.after_or_equal' => 'Tanggal selesai libur tidak boleh lebih awal dari tanggal mulai.',
            ]);

            $start = new \DateTime($tanggalMulai);
            $end = new \DateTime($tanggalSelesai ?: $tanggalMulai);
            $end->modify('+1 day');
            $period = new \DatePeriod($start, new \DateInterval('P1D'), $end);

            // Zero-Break Guard: Periksa setiap tanggal dalam rentang libur
            foreach ($period as $dt) {
                $curDate = $dt->format('Y-m-d');

                $conflict = Contract::where('id_photographer', $photographer->id)
                    ->whereIn('status_contract', ['active', 'approved', 'pending_payment'])
                    ->whereHas('bookingDetail', function ($q) use ($curDate) {
                        $q->where('booking_date', $curDate);
                    })
                    ->with(['bookingDetail', 'client'])
                    ->first();

                if ($conflict) {
                    $code = 'KTRK-' . str_pad($conflict->id, 5, '0', STR_PAD_LEFT);
                    $clientName = $conflict->client->nama ?? 'Klien';
                    $formattedDate = date('d M Y', strtotime($curDate));
                    return redirect()->back()->withInput()->with('error', "Gagal menutup tanggal libur: Terdapat pesanan aktif ({$code}) dari klien {$clientName} pada tanggal {$formattedDate}. Anda harus mengajukan Jadwal Ulang (Reschedule) terlebih dahulu sebelum dapat menandai tanggal ini sebagai libur.");
                }
            }

            foreach ($period as $dt) {
                $curDate = $dt->format('Y-m-d');
                PhotographerAvailability::firstOrCreate([
                    'id_photographer' => $photographer->id,
                    'tanggal' => $curDate,
                    'jam_mulai' => '00:00:00',
                    'jam_selesai' => '23:59:59',
                ], [
                    'status' => 'blocked',
                    'keterangan' => trim($request->keterangan) ?: 'Libur seharian penuh',
                ]);
            }
        }

        return redirect()->route('photographer.availability', ['tab' => 'overrides'])->with('success', 'Jadwal penutupan berhasil disimpan!');
    }

    /**
     * Buka Blokir Jadwal.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $photographer = RolePhotographer::firstOrCreate(['id_user' => $user->id], ['nama' => $user->nama]);

        $avail = PhotographerAvailability::where('id_photographer', $photographer->id)->findOrFail($id);
        $avail->delete();

        return redirect()->route('photographer.availability', ['tab' => 'overrides'])->with('success', 'Blokir jadwal berhasil dibuka!');
    }
}
