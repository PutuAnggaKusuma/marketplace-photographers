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

        // Pastikan konfigurasi default 7 hari kerja tersedia (default Libur / is_open = false)
        PhotographerWorkingHour::initializeDefaults($photographer->id);
        $workingHours = $photographer->workingHours;

        $tab = $request->query('tab', 'weekly'); // 'weekly' or 'overrides'
        $search = $request->query('search', '');
        $periode = $request->query('periode', 'all'); // 'all', 'upcoming', 'past'
        $tipe = $request->query('tipe', 'all'); // 'all', 'all_day', 'time_slot'
        $sort = $request->query('sort', 'date_asc');
        $todayDate = date('Y-m-d');
        $query = PhotographerAvailability::where('id_photographer', $photographer->id);

        if ($periode === 'upcoming') {
            $query->where('tanggal', '>=', $todayDate);
        } elseif ($periode === 'past') {
            $query->where('tanggal', '<', $todayDate);
        }

        if ($tipe === 'all_day') {
            $query->where(function ($q) {
                $q->whereNull('jam_mulai')
                  ->orWhereNull('jam_selesai')
                  ->orWhere(function ($sq) {
                      $sq->where('jam_mulai', '00:00:00')
                         ->where('jam_selesai', '23:59:59');
                  });
            });
        } elseif ($tipe === 'time_slot') {
            $query->whereNotNull('jam_mulai')
                  ->whereNotNull('jam_selesai')
                  ->where(function ($q) {
                      $q->where('jam_mulai', '!=', '00:00:00')
                        ->orWhere('jam_selesai', '!=', '23:59:59');
                  });
        }

        if (!empty($search)) {
            $driver = $query->getConnection()->getDriverName();
            $query->where(function ($q) use ($search, $driver) {
                $q->where('keterangan', 'like', "%{$search}%")
                  ->orWhere('tanggal', 'like', "%{$search}%");
                if (in_array($driver, ['mysql', 'mariadb'])) {
                    $q->orWhereRaw("DATE_FORMAT(tanggal, '%d %b %Y') LIKE ?", ["%{$search}%"])
                      ->orWhereRaw("DATE_FORMAT(tanggal, '%d/%m/%Y') LIKE ?", ["%{$search}%"]);
                }
            });
        }

        if ($sort === 'date_desc') {
            $query->orderBy('tanggal', 'desc')->orderBy('jam_mulai', 'asc');
        } elseif ($sort === 'latest') {
            $query->orderBy('created_at', 'desc');
        } elseif ($sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('tanggal', 'asc')->orderBy('jam_mulai', 'asc');
        }

        $perPage = (int) $request->query('per_page', 10);
        if (!in_array($perPage, [10, 20, 50, 100])) $perPage = 10;
        $availabilities = $query->paginate($perPage)->withQueryString();

        // Metrics & Booked Dates
        $allAvailabilities = PhotographerAvailability::where('id_photographer', $photographer->id)->get();
        $totalAllBlocked = $allAvailabilities->count();
        $totalBlocked = $allAvailabilities->where('tanggal', '>=', $todayDate)->count();

        $activeBookings = Contract::where('id_photographer', $photographer->id)
            ->whereIn('status_contract', ['active', 'approved', 'pending_payment'])
            ->with(['bookingDetail', 'client.user'])
            ->get();
        $totalActiveBookings = $activeBookings->count();

        $nearestBlocked = $allAvailabilities->where('tanggal', '>=', $todayDate)->sortBy('tanggal')->first();
        $nextBooking = $activeBookings->filter(function ($b) use ($todayDate) {
            return $b->bookingDetail && $b->bookingDetail->effective_end_date >= $todayDate;
        })->sortBy(function ($b) {
            return $b->bookingDetail->booking_date;
        })->first();
        $nextBookingDate = $nextBooking ? $nextBooking->bookingDetail->date_range_label : 'Belum Ada';

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
            if ($b->bookingDetail && $b->bookingDetail->booking_date) {
                $clientTitle = 'Booking: ' . ($b->client->nama ?? 'Klien');
                if ($b->bookingDetail->is_multi_day) {
                    $period = \Carbon\CarbonPeriod::create($b->bookingDetail->booking_date, $b->bookingDetail->effective_end_date);
                    foreach ($period as $dt) {
                        $calendarEvents[] = [
                            'title' => $clientTitle . ' (Custom Multi-Hari)',
                            'date' => $dt->format('Y-m-d'),
                            'type' => 'booked'
                        ];
                    }
                } else {
                    $calendarEvents[] = [
                        'title' => $clientTitle,
                        'date' => $b->bookingDetail->booking_date,
                        'type' => 'booked'
                    ];
                }
            }
        }

        return view('photographer.availability.index', compact(
            'photographer',
            'workingHours',
            'availabilities',
            'tab',
            'search',
            'periode',
            'tipe',
            'sort',
            'totalAllBlocked',
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

        // 0. Validasi Jam Kerja: Pastikan format jam valid dan jam tutup lebih akhir daripada jam buka
        foreach ($daysInput as $dow => $cfg) {
            if (!empty($cfg['is_open']) && empty($cfg['is_24_hours'])) {
                $bukaVal = substr(trim((string) ($cfg['jam_buka'] ?? '08:00')), 0, 5);
                $tutupVal = substr(trim((string) ($cfg['jam_tutup'] ?? '17:00')), 0, 5);
                $dayNameVal = $cfg['day_name'] ?? ('Hari ke-' . $dow);

                if (!preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $bukaVal) || !preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $tutupVal)) {
                    return redirect()->back()->withInput()->with('error', "Format jam {$dayNameVal} tidak valid.");
                }

                if ($bukaVal >= $tutupVal) {
                    return redirect()->back()->withInput()->with('error', "Format jam {$dayNameVal} tidak valid: Jam tutup ({$tutupVal} WIB) harus lebih akhir daripada jam buka ({$bukaVal} WIB).");
                }
            }
        }

        // 1. Zero-Break Guard: Cek apakah ada booking aktif mendatang yang terbentur oleh perubahan jam operasional baru
        $upcomingContracts = Contract::where('id_photographer', $photographer->id)
            ->whereIn('status_contract', ['active', 'approved', 'pending_payment'])
            ->whereHas('bookingDetail', function ($q) {
                $q->whereRaw('COALESCE(booking_end_date, booking_date) >= ?', [date('Y-m-d')]);
            })
            ->with(['bookingDetail', 'client'])
            ->get();

        foreach ($upcomingContracts as $c) {
            if (!$c->bookingDetail) {
                continue;
            }
            // Penawaran khusus custom (id_service === null) berjam fleksibel di luar jam reguler,
            // sehingga tidak mengunci perubahan jam buka/tutup mingguan selama bukan blokir libur tanggal.
            if ($c->bookingDetail->id_service === null) {
                continue;
            }

            $bDate = $c->bookingDetail->booking_date;
            $dow = (int) Carbon::parse($bDate)->dayOfWeekIso; // 1 = Senin, ..., 7 = Minggu
            $bJamMulai = substr($c->bookingDetail->jam_mulai ?: '08:00', 0, 5);
            $bJamSelesai = substr($c->bookingDetail->jam_selesai ?: $bJamMulai, 0, 5);
            $code = 'KTRK-' . str_pad($c->id, 5, '0', STR_PAD_LEFT);
            $clientName = $c->client->nama ?? 'Klien';

            $newDayConfig = $daysInput[$dow] ?? null;
            $dayName = $newDayConfig['day_name'] ?? ('Hari ke-' . $dow);

            // A. Jika fotografer mematikan hari operasional yang ada pesanan aktifnya
            if (!$newDayConfig || empty($newDayConfig['is_open'])) {
                return redirect()->back()->withInput()->with('error', "Gagal menyimpan jadwal: Ada pesanan aktif ({$code}) dari {$clientName} pada {$dayName} (" . date('d M Y', strtotime($bDate)) . "). Ajukan jadwal ulang terlebih dahulu.");
            }

            // B. Jika fotografer memperpendek jam kerja dan pesanan berada di luar jam operasional baru
            if (empty($newDayConfig['is_24_hours'])) {
                $newBuka = substr(trim((string) ($newDayConfig['jam_buka'] ?? '08:00')), 0, 5);
                $newTutup = substr(trim((string) ($newDayConfig['jam_tutup'] ?? '17:00')), 0, 5);

                if ($bJamMulai < $newBuka || $bJamSelesai > $newTutup) {
                    return redirect()->back()->withInput()->with('error', "Gagal menyimpan jadwal: Pesanan aktif ({$code}) pada {$dayName} (" . date('d M Y', strtotime($bDate)) . ") jam {$bJamMulai} - {$bJamSelesai} WIB berada di luar jam operasional baru ({$newBuka} - {$newTutup} WIB). Ajukan jadwal ulang terlebih dahulu.");
                }
            }
        }

        // 2. Simpan konfigurasi jam kerja 7 hari
        $hasOpenDay = collect($daysInput)->contains(fn($cfg) => !empty($cfg['is_open']));

        foreach ($daysInput as $dow => $cfg) {
            $isOpen = !empty($cfg['is_open']);
            $is24Hours = $isOpen && !empty($cfg['is_24_hours']);
            $bukaClean = substr(trim((string) ($cfg['jam_buka'] ?? '08:00')), 0, 5);
            $tutupClean = substr(trim((string) ($cfg['jam_tutup'] ?? '17:00')), 0, 5);
            $jamBuka = preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $bukaClean) ? $bukaClean . ':00' : '08:00:00';
            $jamTutup = preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $tutupClean) ? $tutupClean . ':00' : '17:00:00';

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
            'jadwal_dikonfirmasi' => $hasOpenDay,
        ]);

        $downgraded = $photographer->fresh()->syncStorefrontIntegrity();

        if (!$hasOpenDay) {
            $warningMsg = $downgraded
                ? 'Semua hari ditandai libur sehingga etalase dinonaktifkan sementara.'
                : 'Semua hari ditandai libur. Buka minimal satu hari untuk mengaktifkan jadwal.';

            return redirect()->route('photographer.availability', ['tab' => 'weekly'])->with('warning', $warningMsg);
        }

        return redirect()->route('photographer.availability', ['tab' => 'weekly'])->with('success', 'Jadwal operasional berhasil diperbaharui.');
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

            $startSlot = substr(trim((string) $request->jam_mulai), 0, 5);
            $endSlot = substr(trim((string) $request->jam_selesai), 0, 5);
            if (!preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $startSlot) || !preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $endSlot)) {
                return redirect()->back()->withInput()->with('error', 'Format jam penutupan jadwal tidak valid.');
            }
            if ($startSlot >= $endSlot) {
                return redirect()->back()->withInput()->with('error', "Jam selesai penutupan jadwal ({$endSlot} WIB) harus lebih akhir daripada jam mulai ({$startSlot} WIB).");
            }

            $startTimeDb = $startSlot . ':00';
            $endTimeDb = $endSlot . ':00';

            // Zero-Break Guard: Cek apakah ada pesanan aktif pada tanggal/rentang jam tersebut (termasuk multi-hari)
            $conflict = Contract::where('id_photographer', $photographer->id)
                ->whereIn('status_contract', ['active', 'approved', 'pending_payment'])
                ->whereHas('bookingDetail', function ($q) use ($request, $startTimeDb, $endTimeDb) {
                    $q->whereDate('booking_date', '<=', $request->tanggal)
                      ->whereRaw('COALESCE(booking_end_date, booking_date) >= ?', [$request->tanggal])
                      ->where(function ($sub) use ($startTimeDb, $endTimeDb) {
                          $sub->whereColumn('booking_end_date', '>', 'booking_date')
                              ->orWhere(function ($timeQ) use ($startTimeDb, $endTimeDb) {
                                  $timeQ->where('jam_mulai', '<', $endTimeDb)
                                        ->where('jam_selesai', '>', $startTimeDb);
                              });
                      });
                })
                ->with(['bookingDetail', 'client'])
                ->first();

            if ($conflict) {
                $code = 'KTRK-' . str_pad($conflict->id, 5, '0', STR_PAD_LEFT);
                $clientName = $conflict->client->nama ?? 'Klien';
                $slotTime = substr($conflict->bookingDetail->jam_mulai, 0, 5) . ' - ' . substr($conflict->bookingDetail->jam_selesai, 0, 5);
                return redirect()->back()->withInput()->with('error', "Gagal menutup slot jam: Ada pesanan aktif ({$code}) dari {$clientName} pada jam {$slotTime} WIB. Ajukan jadwal ulang terlebih dahulu.");
            }

            PhotographerAvailability::create([
                'id_photographer' => $photographer->id,
                'tanggal' => $request->tanggal,
                'jam_mulai' => $startTimeDb,
                'jam_selesai' => $endTimeDb,
                'status' => 'blocked',
                'keterangan' => trim($request->keterangan) ?: "Penutupan jam {$startSlot} - {$endSlot} WIB",
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
                        $q->whereDate('booking_date', '<=', $curDate)
                          ->whereRaw('COALESCE(booking_end_date, booking_date) >= ?', [$curDate]);
                    })
                    ->with(['bookingDetail', 'client'])
                    ->first();

                if ($conflict) {
                    $code = 'KTRK-' . str_pad($conflict->id, 5, '0', STR_PAD_LEFT);
                    $clientName = $conflict->client->nama ?? 'Klien';
                    $formattedDate = date('d M Y', strtotime($curDate));
                    return redirect()->back()->withInput()->with('error', "Gagal menutup tanggal libur: Ada pesanan aktif ({$code}) dari {$clientName} pada {$formattedDate}. Ajukan jadwal ulang terlebih dahulu.");
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

        return redirect()->route('photographer.availability', ['tab' => 'overrides'])->with('success', 'Tanggal libur berhasil disimpan.');
    }

    /**
     * Buka Blokir Jadwal.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $photographer = RolePhotographer::firstOrCreate(['id_user' => $user->id], ['nama' => $user->nama]);

        $avail = PhotographerAvailability::where('id_photographer', $photographer->id)->findOrFail($id);
        $isPast = $avail->tanggal && $avail->tanggal < date('Y-m-d');
        $avail->delete();

        $msg = $isPast ? 'Riwayat tanggal libur berhasil dihapus.' : 'Penutupan jadwal berhasil dibuka.';
        return redirect()->route('photographer.availability', ['tab' => 'overrides'])->with('success', $msg);
    }
}
