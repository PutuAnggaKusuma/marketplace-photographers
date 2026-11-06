<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
        $statusFilter = $request->query('status', 'all'); // all, verified, unverified
        $categoryFilter = $request->query('category', 'all');
        $sort = $request->query('sort', 'latest');
        $perPage = (int) $request->query('per_page', 10);
        if (!in_array($perPage, [10, 20, 50, 100])) $perPage = 10;

        $query = RolePhotographer::with(['user', 'city', 'province', 'categories', 'services', 'testimonials']);

        if ($statusFilter === 'verified') {
            $query->where('is_verified', true);
        } elseif ($statusFilter === 'unverified') {
            $query->where('is_verified', false);
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
        $categories = \App\Models\Category::all();

        // 4 Clean Summary Metric Stats
        $totalStudios = RolePhotographer::count();
        $totalVerified = RolePhotographer::where('is_verified', true)->count();
        $totalUnverified = RolePhotographer::where('is_verified', false)->count();
        $totalServices = \App\Models\PhotographerService::count();

        return view('admin.photographers.index', compact(
            'photographers',
            'categories',
            'search',
            'statusFilter',
            'categoryFilter',
            'sort',
            'totalStudios',
            'totalVerified',
            'totalUnverified',
            'totalServices'
        ));
    }

    public function toggleVerification(Request $request, $id)
    {
        $photographer = RolePhotographer::findOrFail($id);
        $newStatus = !$photographer->is_verified;
        $photographer->update(['is_verified' => $newStatus]);

        $statusText = $newStatus ? 'diverifikasi (Centang Biru Aktif)' : 'dibatalkan verifikasinya';

        \App\Models\ActivityLog::record(
            'photographer_verified',
            "Admin mengubah status verifikasi studio '{$photographer->nama}' menjadi: {$statusText}",
            $photographer,
            ['is_verified' => $newStatus]
        );

        // Notifikasi In-App ke Fotografer
        if ($photographer->id_user) {
            $notifTitle = $newStatus ? 'Profil Fotografer Diverifikasi' : 'Status Verifikasi Dinonaktifkan';
            $notifMsg = $newStatus
                ? "Selamat! Profil fotografer '{$photographer->nama}' telah resmi diverifikasi oleh LensMatch. Lencana centang biru kini aktif pada profil dan katalog publik Anda."
                : "Status verifikasi centang biru pada profil studio '{$photographer->nama}' telah dinonaktifkan oleh Admin.";

            \App\Models\Notification::send(
                $photographer->id_user,
                $notifTitle,
                $notifMsg,
                'photographer_verified',
                url('/photographer/dashboard')
            );
        }

        return redirect()->back()->with('success', "Status studio '{$photographer->nama}' berhasil {$statusText}.");
    }

    /**
     * Hapus Mitra Fotografer & Akun Terkait.
     */
    public function destroy($id)
    {
        $photographer = RolePhotographer::findOrFail($id);

        $hasActive = \App\Models\Contract::where('id_photographer', $photographer->id)
            ->whereIn('status_contract', ['pending', 'active'])
            ->exists();
        if ($hasActive) {
            return redirect()->back()->with('error', "Studio '{$photographer->nama}' tidak dapat dihapus karena masih memiliki pesanan sesi aktif yang sedang berjalan.");
        }

        $user = $photographer->user;

        \App\Models\ActivityLog::record(
            'photographer_deleted',
            "Admin menghapus studio '{$photographer->nama}' dan akun terkait",
            $photographer
        );

        $photographer->delete();
        if ($user) {
            $user->delete();
        }

        return redirect()->back()->with('success', "Studio '{$photographer->nama}' dan akun terkait berhasil dihapus.");
    }
}