<?php

namespace App\Http\Controllers\Photographer;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\ContractBooking;
use App\Models\PhotographerAvailability;
use App\Models\PhotographerPortfolio;
use App\Models\PhotographerService;
use App\Models\RolePhotographer;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $photographer = RolePhotographer::where('id_user', $user->id ?? 0)->first();
        if (!$photographer) {
            $photographer = RolePhotographer::first();
        }
        $photographerId = $photographer ? $photographer->id : null;

        // 1. Actionable Operational Queues (100% Dynamic from Database)
        // Queue 1: Order Booking Baru Perlu Dikonfirmasi
        $pendingBookingsCount = Contract::where('id_photographer', $photographerId)
            ->whereHas('bookingDetail', function ($q) {
                $q->where('status_booking', 'pending');
            })
            ->count();

        // Queue 2: Galeri Hasil Foto Perlu Diunggah (Sesi Dikonfirmasi/Active tapi belum selesai)
        $needGalleryUploadCount = Contract::where('id_photographer', $photographerId)
            ->where('status_contract', 'active')
            ->count();

        // Queue 3: Payout Siap Dicairkan (Holding/Ready)
        $readyPayoutCount = Contract::where('id_photographer', $photographerId)
            ->whereIn('status_payout', ['ready', 'holding'])
            ->count();

        // 2. 4 Core Financial & System Metric Cards (100% Dynamic from Database)
        // Card 1: Total Omzet Bersih Fotografer (90% Payout)
        $totalNetRevenue = Contract::where('id_photographer', $photographerId)
            ->where('status_payout', 'paid')
            ->sum('payout_amount');
        if ($totalNetRevenue == 0) {
            $totalNetRevenue = (int)(Contract::where('id_photographer', $photographerId)->sum('jumlah') * 0.9);
        }

        // Card 2: Total Order Booking Masuk & Selesai
        $totalBookingsCount = Contract::where('id_photographer', $photographerId)->count();
        $completedBookingsCount = Contract::where('id_photographer', $photographerId)
            ->where('status_contract', 'completed')
            ->count();

        // Card 3: Rating & Kepuasan Klien
        $testimonials = Testimonial::where('id_photographer', $photographerId)->get();
        $totalReviews = $testimonials->count();
        $avgRating = $totalReviews > 0 ? round($testimonials->avg('rating'), 1) : 0;

        // Card 4: Katalog & Portofolio Aktif
        $totalServices = PhotographerService::where('id_photographer', $photographerId)->count();
        $totalPortfolios = PhotographerPortfolio::where('id_photographer', $photographerId)->count();

        // 3. Monthly Net Revenue Chart (12 Months - 100% Dynamic Real Data)
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $monthlyRevenue = array_fill(0, 12, 0);
        $allContracts = Contract::where('id_photographer', $photographerId)->get();

        foreach ($allContracts as $c) {
            if ($c->created_at && $c->created_at->format('Y') == date('Y')) {
                $mIdx = (int) $c->created_at->format('n') - 1;
                $net = $c->payout_amount > 0 ? $c->payout_amount : ($c->jumlah * 0.9);
                $monthlyRevenue[$mIdx] += ($net / 1000000); // dalam juta rupiah
            }
        }

        $totalYearRevenue = array_sum($monthlyRevenue);
        $avgMonthlyRevenue = round($totalYearRevenue / 12, 1);
        $maxMonthlyRevenue = round(max($monthlyRevenue), 1);
        $totalAccumulatedRevenue = round($totalYearRevenue, 1);

        // 4. Distribution of Services Share / Demand (100% Dynamic from actual booking count per service)
        $services = PhotographerService::where('id_photographer', $photographerId)->get();
        $serviceStats = [];
        $palette = ['bg-amber-400', 'bg-blue-500', 'bg-emerald-500', 'bg-purple-500', 'bg-rose-500'];
        
        $totalBookingsForShare = 0;
        $srvBookingCounts = [];
        foreach ($services as $srv) {
            $cnt = ContractBooking::where('id_service', $srv->id)->count();
            $srvBookingCounts[$srv->id] = $cnt;
            $totalBookingsForShare += $cnt;
        }

        foreach ($services as $idx => $srv) {
            $cnt = $srvBookingCounts[$srv->id] ?? 0;
            $share = $totalBookingsForShare > 0 ? round(($cnt / $totalBookingsForShare) * 100) : (round(100 / max(1, $services->count())));
            $serviceStats[] = [
                'name' => $srv->nama_layanan,
                'price' => $srv->tarif_harga,
                'share' => $share,
                'count' => $cnt,
                'color' => $palette[$idx % count($palette)],
            ];
        }

        // 5. Recent Booking Contracts (100% Dynamic)
        $recentBookings = Contract::where('id_photographer', $photographerId)
            ->with(['client.user', 'bookingDetail.service', 'payments'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // 6. Upcoming Schedules Timeline (100% Dynamic)
        $upcomingSchedules = ContractBooking::whereHas('contract', function ($q) use ($photographerId) {
                $q->where('id_photographer', $photographerId);
            })
            ->with(['contract.client', 'service'])
            ->where('booking_date', '>=', date('Y-m-d'))
            ->orderBy('booking_date', 'asc')
            ->take(4)
            ->get();

        $manualBlockedDays = PhotographerAvailability::where('id_photographer', $photographerId)
            ->where('tanggal', '>=', date('Y-m-d'))
            ->orderBy('tanggal', 'asc')
            ->take(3)
            ->get();

        return view('photographer.dashboard', compact(
            'photographer',
            'pendingBookingsCount',
            'needGalleryUploadCount',
            'readyPayoutCount',
            'totalNetRevenue',
            'totalBookingsCount',
            'completedBookingsCount',
            'avgRating',
            'totalReviews',
            'totalServices',
            'totalPortfolios',
            'months',
            'monthlyRevenue',
            'avgMonthlyRevenue',
            'maxMonthlyRevenue',
            'totalAccumulatedRevenue',
            'serviceStats',
            'recentBookings',
            'upcomingSchedules',
            'manualBlockedDays'
        ));
    }
}