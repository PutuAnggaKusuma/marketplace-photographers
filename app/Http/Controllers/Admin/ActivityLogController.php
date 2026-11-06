<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    /**
     * Tampilkan Halaman Audit Trail & Log Aktivitas Sistem.
     */
    public function index(Request $request)
    {
        $search = $request->query('search', '');
        $logFilter = $request->query('log_name', 'all');
        $sort = $request->query('sort', 'latest');
        $perPage = (int) $request->query('per_page', 10);
        if (!in_array($perPage, [10, 20, 50, 100])) $perPage = 10;

        $query = ActivityLog::with('user');

        if ($logFilter !== 'all') {
            $query->where('log_name', $logFilter);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('log_name', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('nama', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $logs = $query->paginate($perPage)->withQueryString();

        // 4 Summary Metrics
        $totalLogs = ActivityLog::count();
        $totalTransactions = ActivityLog::whereIn('log_name', ['payout_released', 'refund_processed', 'payment_confirmed'])->count();
        $totalBookings = ActivityLog::whereIn('log_name', ['booking_created', 'booking_confirmed', 'booking_rejected', 'booking_cancelled', 'booking_completed', 'gallery_uploaded'])->count();
        $totalSystem = ActivityLog::whereIn('log_name', ['photographer_verified', 'photographer_deleted', 'admin_created', 'category_created'])->count();

        // Unique Log Types for Filter Dropdown
        $logTypes = ActivityLog::select('log_name')
            ->distinct()
            ->whereNotNull('log_name')
            ->pluck('log_name');

        return view('admin.activity_logs.index', compact(
            'logs',
            'search',
            'logFilter',
            'sort',
            'totalLogs',
            'totalTransactions',
            'totalBookings',
            'totalSystem',
            'logTypes'
        ));
    }

    /**
     * Bersihkan Log Aktivitas Lama (Khusus Super Admin).
     */
    public function clearOld(Request $request)
    {
        if (!Auth::check() || Auth::user()->role !== 'super_admin') {
            abort(403, 'Akses Ditolak. Fitur pembersihan log hanya dapat diakses oleh Super Admin.');
        }

        $days = (int) $request->input('days', 30);
        $deleted = ActivityLog::where('created_at', '<', now()->subDays($days))->delete();

        return redirect()->back()->with('success', "Sebanyak {$deleted} riwayat log aktivitas yang lebih lama dari {$days} hari berhasil dibersihkan.");
    }
}
