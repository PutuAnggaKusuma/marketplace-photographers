<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ContestRegistration;
use App\Models\Contract;
use App\Models\ELearningProgress;
use App\Models\Notification;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Tampilkan Tabel Pengelolaan Akun Klien (Penyewa Jasa).
     */
    public function index(Request $request)
    {
        $search = $request->query('search', '');
        $status = $request->query('status', 'all');
        $sort = $request->query('sort', 'latest');
        $perPage = (int) $request->query('per_page', 10);
        if (!in_array($perPage, [10, 20, 50, 100])) $perPage = 10;

        // Khusus Akun Klien Saja
        $query = User::where('role', 'client')->with(['roleClient']);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('roleClient', function ($rc) use ($search) {
                      $rc->where('nomor_telepon', 'like', "%{$search}%")
                         ->orWhere('alamat', 'like', "%{$search}%");
                  });
            });
        }

        // Filter Status Akun
        if ($status === 'active') {
            $query->where('is_suspended', false);
        } elseif ($status === 'suspended') {
            $query->where('is_suspended', true);
        } elseif ($status === 'verified') {
            $query->whereNotNull('email_verified_at');
        } elseif ($status === 'unverified') {
            $query->whereNull('email_verified_at');
        } elseif ($status === 'activation_requested') {
            $query->whereNotNull('activation_requested_at')->whereNull('email_verified_at');
        }

        // Pengurutan Data
        if ($sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } elseif ($sort === 'name_asc') {
            $query->orderBy('nama', 'asc');
        } elseif ($sort === 'activation_requested') {
            $query->orderByRaw('activation_requested_at IS NULL, activation_requested_at DESC');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $users = $query->paginate($perPage)->withQueryString();

        // 4 Summary Metric Stats Khusus Klien (Rule #64 Compliant)
        $totalClients = User::where('role', 'client')->count();
        $totalVerifiedClients = User::where('role', 'client')->whereNotNull('email_verified_at')->count();
        $totalPendingActivation = User::where('role', 'client')
            ->whereNotNull('activation_requested_at')
            ->whereNull('email_verified_at')
            ->count();
        $totalSpending = Contract::whereIn('status_contract', ['active', 'completed'])->sum('jumlah');

        return view('admin.users.index', compact(
            'users',
            'search',
            'status',
            'sort',
            'totalClients',
            'totalVerifiedClients',
            'totalPendingActivation',
            'totalSpending'
        ));
    }

    /**
     * Tampilkan Detail Profil & Riwayat Transaksi Klien (JSON untuk Modal).
     */
    public function show(Request $request, $id)
    {
        $user = User::where('role', 'client')->with(['roleClient'])->findOrFail($id);
        $roleClient = $user->roleClient;

        // Hitung Statistik Personal Klien
        $clientId = $roleClient?->id;
        $totalContracts = $clientId ? Contract::where('id_client', $clientId)->count() : 0;
        $totalSpent = $clientId ? Contract::where('id_client', $clientId)->whereIn('status_contract', ['active', 'completed'])->sum('jumlah') : 0;
        $totalReviews = $clientId ? Testimonial::where('id_client', $clientId)->count() : 0;
        $totalContests = ContestRegistration::where('id_user', $user->id)->count();
        $totalCourses = ELearningProgress::where('id_user', $user->id)->count();

        // 5 Riwayat Pesanan Booking Terakhir
        $recentBookings = [];
        if ($clientId) {
            $contracts = Contract::with(['photographer.user', 'bookingDetail.service'])
                ->where('id_client', $clientId)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();

            $statusLabels = [
                'draft' => 'Draft',
                'approved' => 'Menunggu Pembayaran',
                'pending_payment' => 'Menunggu Pembayaran',
                'active' => 'Aktif Berjalan',
                'completed' => 'Selesai',
                'cancelled' => 'Dibatalkan',
            ];

            foreach ($contracts as $c) {
                $recentBookings[] = [
                    'id' => $c->id,
                    'code' => '#KTRK-' . str_pad($c->id, 5, '0', STR_PAD_LEFT),
                    'photographer_name' => $c->photographer?->nama ?? $c->photographer?->user?->nama ?? 'Mitra Fotografer',
                    'service_name' => $c->bookingDetail?->service?->nama_layanan ?? 'Sesi Fotografi',
                    'amount_formatted' => 'Rp ' . number_format($c->jumlah, 0, ',', '.'),
                    'status_contract' => $c->status_contract,
                    'status_label' => $statusLabels[$c->status_contract] ?? ucfirst($c->status_contract),
                    'date_formatted' => $c->bookingDetail?->booking_date ? \Carbon\Carbon::parse($c->bookingDetail->booking_date)->format('d M Y') : $c->created_at->format('d M Y'),
                    'invoice_url' => route('admin.transactions.show', $c->id),
                ];
            }
        }

        $phone = $roleClient?->nomor_telepon ?: null;

        $payload = [
            'id' => $user->id,
            'nama' => $user->nama,
            'email' => $user->email,
            'phone' => $phone ?: '-',
            'alamat' => $roleClient?->alamat ?: 'Belum diisi',
            'avatar_url' => $user->avatar_url,
            'initials' => $user->initials,
            'joined_at' => $user->created_at->format('d M Y'),
            'email_verified' => !is_null($user->email_verified_at),
            'email_verified_at' => $user->email_verified_at ? $user->email_verified_at->format('d M Y H:i') : null,
            'is_suspended' => (bool) $user->is_suspended,
            'suspension_reason' => $user->suspension_reason,
            'activation_requested' => !is_null($user->activation_requested_at) && is_null($user->email_verified_at),
            'activation_requested_at' => $user->activation_requested_at ? $user->activation_requested_at->format('d M Y H:i') : null,
            'activation_request_note' => $user->activation_request_note,
            'stats' => [
                'total_bookings' => $totalContracts,
                'total_spent_formatted' => 'Rp ' . number_format($totalSpent, 0, ',', '.'),
                'total_reviews' => $totalReviews,
                'total_contests' => $totalContests,
                'total_courses' => $totalCourses,
            ],
            'recent_bookings' => $recentBookings,
            'can_delete' => $totalContracts === 0,
        ];

        return response()->json($payload);
    }

    /**
     * Bantuan CS: Verifikasi Email Manual oleh Admin.
     */
    public function verifyEmail(Request $request, $id)
    {
        $user = User::where('role', 'client')->findOrFail($id);

        $user->update([
            'email_verified_at' => now(),
            'activation_requested_at' => null,
            'activation_request_note' => null,
        ]);

        ActivityLog::record(
            'client_verified',
            "Admin memverifikasi email akun klien '{$user->nama}' ({$user->email}) secara manual.",
            $user
        );

        Notification::send(
            $user->id,
            'Akun Berhasil Diverifikasi',
            "Akun Anda telah berhasil diverifikasi oleh admin. Selamat datang di LensMatch!",
            'activation_verified',
            url('/')
        );

        return redirect()->back()->with('success', "Email akun klien '{$user->nama}' berhasil diverifikasi.");
    }

    /**
     * Moderasi Akun: Bekukan atau Aktifkan Kembali Akun Klien.
     */
    public function toggleSuspend(Request $request, $id)
    {
        $user = User::where('role', 'client')->findOrFail($id);

        if (!$user->is_suspended) {
            $reason = trim((string) $request->input('reason', ''));
            if (empty($reason)) {
                $reason = 'Ditangguhkan oleh admin.';
            }

            $user->update([
                'is_suspended' => true,
                'suspension_reason' => $reason,
            ]);

            ActivityLog::record(
                'client_suspended',
                "Admin membekukan akun klien '{$user->nama}'. Alasan: {$reason}",
                $user
            );

            Notification::send(
                $user->id,
                'Pemberitahuan Akun Ditangguhkan',
                "Akun Anda ditangguhkan oleh admin. Alasan: {$reason}. Silakan hubungi admin jika terdapat kekeliruan.",
                'account_suspended'
            );

            return redirect()->back()->with('success', "Akun klien '{$user->nama}' berhasil dibekukan.");
        } else {
            $user->update([
                'is_suspended' => false,
                'suspension_reason' => null,
            ]);

            ActivityLog::record(
                'client_unsuspended',
                "Admin mengaktifkan kembali akun klien '{$user->nama}'.",
                $user
            );

            Notification::send(
                $user->id,
                'Akun Diaktifkan Kembali',
                "Akun Anda telah diaktifkan kembali oleh admin. Anda dapat menggunakan akun Anda seperti biasa.",
                'account_activated',
                url('/')
            );

            return redirect()->back()->with('success', "Akun klien '{$user->nama}' berhasil diaktifkan kembali.");
        }
    }

    /**
     * Hapus Akun Klien (Hanya jika belum pernah memiliki transaksi/kontrak).
     */
    public function destroy($id)
    {
        $user = User::where('role', 'client')->findOrFail($id);
        $clientId = $user->roleClient?->id;

        $hasContracts = $clientId ? Contract::where('id_client', $clientId)->exists() : false;

        if ($hasContracts) {
            return redirect()->back()->with('error', "Akun klien '{$user->nama}' tidak dapat dihapus karena memiliki riwayat transaksi/booking. Anda dapat menggunakan aksi 'Bekukan Akun' untuk menonaktifkannya.");
        }

        ActivityLog::record(
            'client_deleted',
            "Admin menghapus akun klien '{$user->nama}' ({$user->email}).",
            $user
        );

        $user->roleClient?->delete();
        $user->delete();

        return redirect()->back()->with('success', "Akun klien '{$user->nama}' berhasil dihapus.");
    }
}
