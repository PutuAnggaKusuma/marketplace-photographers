<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Contract;
use App\Models\Notification;
use App\Models\PhotographerService;
use App\Models\RolePhotographer;
use App\Models\User;
use Illuminate\Http\Request;

class PhotographerVerificationController extends Controller
{
    /**
     * Tampilkan Halaman Manajemen & Verifikasi Mitra Fotografer.
     */
    public function index(Request $request)
    {
        $search = $request->query('search', '');
        $statusFilter = $request->query('status', 'all'); // all, pending, verified, rejected
        $categoryFilter = $request->query('category', 'all');
        $sort = $request->query('sort', 'latest');
        $perPage = (int) $request->query('per_page', 10);
        if (!in_array($perPage, [10, 20, 50, 100])) $perPage = 10;

        $query = RolePhotographer::with(['user', 'city', 'province', 'categories', 'services', 'testimonials']);

        if (in_array($statusFilter, ['pending', 'menunggu'])) {
            $query->where('status_verifikasi', 'menunggu');
        } elseif (in_array($statusFilter, ['verified', 'terverifikasi'])) {
            $query->where('status_verifikasi', 'terverifikasi');
        } elseif (in_array($statusFilter, ['rejected', 'ditolak'])) {
            $query->where('status_verifikasi', 'ditolak');
        }

        if ($categoryFilter !== 'all') {
            $query->whereHas('categories', function($q) use ($categoryFilter) {
                $q->where('categories.id', $categoryFilter);
            });
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('deskripsi_bio', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('nama', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('city', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } elseif ($sort === 'name_asc') {
            $query->orderBy('nama', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $photographers = $query->paginate($perPage)->withQueryString();
        $categories = Category::all();

        // Metrik Ringkasan Status Pendaftaran
        $totalStudios = RolePhotographer::count();
        $totalVerified = RolePhotographer::where('status_verifikasi', 'terverifikasi')->count();
        $totalPending = RolePhotographer::where('status_verifikasi', 'menunggu')->count();
        $totalRejected = RolePhotographer::where('status_verifikasi', 'ditolak')->count();

        return view('admin.photographers.index', compact(
            'photographers',
            'categories',
            'search',
            'statusFilter',
            'categoryFilter',
            'sort',
            'totalStudios',
            'totalVerified',
            'totalPending',
            'totalRejected'
        ));
    }

    /**
     * Tampilkan Detail Audit & Verifikasi Registrasi Mitra Fotografer.
     */
    public function show($id)
    {
        $photographer = RolePhotographer::with([
            'user',
            'province',
            'city',
            'categories',
            'services.details',
            'portfolios.medias',
            'testimonials.client.user'
        ])->findOrFail($id);

        $missingFields = $photographer->getMissingProfileFields();
        $completionPercentage = $photographer->getProfileCompletionPercentage();
        $isComplete = $photographer->isProfileComplete();

        return view('admin.photographers.show', compact(
            'photographer',
            'missingFields',
            'completionPercentage',
            'isComplete'
        ));
    }

    /**
     * Setujui / Batalkan Verifikasi Fotografer.
     */
    public function toggleVerification(Request $request, $id)
    {
        $photographer = RolePhotographer::findOrFail($id);
        $wasVerified = ($photographer->status_verifikasi === 'terverifikasi');
        $newVerified = !$wasVerified;

        $photographer->update([
            'status_verifikasi' => $newVerified ? 'terverifikasi' : 'menunggu',
            'is_verified' => $newVerified,
            'catatan_penolakan' => null,
        ]);

        $statusText = $newVerified ? 'diverifikasi' : 'dibatalkan verifikasinya';

        ActivityLog::record(
            'photographer_verified',
            "Admin mengubah status verifikasi '{$photographer->nama}' menjadi: {$statusText}",
            $photographer,
            ['status_verifikasi' => $photographer->status_verifikasi]
        );

        // Notifikasi In-App ke Fotografer
        if ($photographer->id_user) {
            $notifTitle = $newVerified ? 'Pendaftaran Mitra Terverifikasi' : 'Status Verifikasi Dinonaktifkan';
            $notifMsg = $newVerified
                ? "Selamat! Akun fotografer '{$photographer->nama}' telah resmi disetujui dan diverifikasi oleh LensMatch."
                : "Status verifikasi pada profil studio '{$photographer->nama}' telah dinonaktifkan oleh Admin.";

            Notification::send(
                $photographer->id_user,
                $notifTitle,
                $notifMsg,
                'photographer_verified',
                url('/photographer/dashboard')
            );
        }

        return redirect()->back()->with('success', "Status mitra '{$photographer->nama}' berhasil {$statusText}.");
    }

    /**
     * Tolak Pendaftaran Mitra dengan Catatan Perbaikan.
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'catatan_penolakan' => 'required|string|min:5|max:1000',
        ], [
            'catatan_penolakan.required' => 'Catatan alasan penolakan wajib diisi.',
            'catatan_penolakan.min' => 'Catatan alasan penolakan minimal 5 karakter.',
        ]);

        $photographer = RolePhotographer::findOrFail($id);

        $photographer->update([
            'status_verifikasi' => 'ditolak',
            'is_verified' => false,
            'catatan_penolakan' => trim($request->catatan_penolakan),
        ]);

        ActivityLog::record(
            'photographer_rejected',
            "Admin menolak pendaftaran mitra '{$photographer->nama}'. Catatan: {$photographer->catatan_penolakan}",
            $photographer,
            ['catatan_penolakan' => $photographer->catatan_penolakan]
        );

        // Notifikasi In-App ke Fotografer
        if ($photographer->id_user) {
            Notification::send(
                $photographer->id_user,
                'Pendaftaran Mitra Ditolak',
                "Pendaftaran Anda belum dapat disetujui. Catatan: {$photographer->catatan_penolakan}. Silakan perbaiki data profil Anda.",
                'photographer_rejected',
                url('/photographer/profile')
            );
        }

        return redirect()->back()->with('success', "Pendaftaran mitra '{$photographer->nama}' berhasil ditolak dengan catatan.");
    }

    /**
     * Hapus Mitra Fotografer & Akun Terkait dengan Kategori & Alasan Pelanggaran.
     */
    public function destroy(Request $request, $id)
    {
        $request->validate([
            'kategori_penghapusan' => 'required|string|max:100',
            'alasan_penghapusan' => 'required|string|min:5|max:1000',
        ], [
            'kategori_penghapusan.required' => 'Kategori pelanggaran wajib dipilih.',
            'alasan_penghapusan.required' => 'Deskripsi alasan penghapusan wajib diisi.',
            'alasan_penghapusan.min' => 'Deskripsi alasan penghapusan minimal 5 karakter.',
        ]);

        $photographer = RolePhotographer::findOrFail($id);

        $hasActive = Contract::where('id_photographer', $photographer->id)
            ->whereIn('status_contract', ['pending', 'active'])
            ->exists();

        if ($hasActive) {
            return redirect()->back()->with('error', "Studio '{$photographer->nama}' tidak dapat dihapus karena masih memiliki pesanan sesi aktif yang sedang berjalan.");
        }

        $user = $photographer->user;

        // Simpan alasan sebelum soft delete
        $photographer->update([
            'kategori_penghapusan' => $request->kategori_penghapusan,
            'alasan_penghapusan' => trim($request->alasan_penghapusan),
        ]);

        ActivityLog::record(
            'photographer_deleted',
            "Admin menghapus studio '{$photographer->nama}' karena pelanggaran: [{$request->kategori_penghapusan}] - {$request->alasan_penghapusan}",
            $photographer,
            [
                'kategori_penghapusan' => $request->kategori_penghapusan,
                'alasan_penghapusan' => $request->alasan_penghapusan,
            ]
        );

        $photographer->delete();
        if ($user) {
            $user->delete();
        }

        return redirect()->route('admin.photographers')->with('success', "Studio '{$photographer->nama}' dan akun terkait berhasil dihapus.");
    }
}
