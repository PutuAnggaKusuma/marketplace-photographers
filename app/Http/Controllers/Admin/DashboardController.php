<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Contract;
use App\Models\Payment;
use App\Models\PhotoContest;
use App\Models\RolePhotographer;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Core Metrics (100% Dynamic from Database)
        $totalUsers = User::count();
        $totalClients = User::where('role', 'client')->count();
        $totalPhotographers = User::where('role', 'photographer')->count();
        $verifiedPhotographers = RolePhotographer::where('status_verifikasi', 'terverifikasi')->count();
        $totalContracts = Contract::count();
        
        $elearningRevenue = \App\Models\ElearningTransaction::where('payment_status', 'completed')->sum('nominal');
        $paidContests = \App\Models\ContestRegistration::where('status_pembayaran', 'lunas')->with('contest')->get();
        $contestRevenue = $paidContests->sum(fn($r) => (float) ($r->contest->biaya_pendaftaran ?? 0));

        $settledContracts = Contract::where(function ($q) {
            $q->whereIn('status_contract', ['active', 'completed'])
              ->orWhereIn('status_payout', ['holding', 'ready', 'paid']);
        })->whereHas('payments', function ($p) {
            $p->where('payment_status', 'completed');
        })->get();

        $grossRevenue = $settledContracts->sum('jumlah') + $elearningRevenue + $contestRevenue;
        $platformFee = $settledContracts->sum(fn($c) => $c->fee_marketplace > 0 ? $c->fee_marketplace : ($c->jumlah * 0.10)) + $elearningRevenue + $contestRevenue;
        $escrowHolding = Contract::where('status_payout', 'holding')
            ->where('status_contract', '!=', 'cancelled')
            ->whereHas('payments', function ($p) {
                $p->where('payment_status', 'completed');
            })->sum('payout_amount');
        
        // 2. Actionable Operational Alerts (100% Dynamic)
        $pendingVerifications = RolePhotographer::where(function ($q) {
            $q->where('status_verifikasi', 'menunggu')
              ->orWhere(function ($q2) {
                  $q2->where('status_verifikasi', 'terverifikasi')
                     ->where('status_etalase', 'menunggu');
              });
        })->count();
        $pendingPayouts = Contract::where('status_payout', 'ready')->count();
        $pendingContestReviews = PhotoContest::where('status', 'pending_review')->count();

        // 3. Monthly Financial & Booking Trends (Jan - Des Tahun Berjalan)
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $monthlyRevenue = array_fill(0, 12, 0);
        $monthlyCommission = array_fill(0, 12, 0);
        $currentYear = (int) date('Y');

        foreach ($settledContracts as $c) {
            if ($c->created_at && (int) $c->created_at->format('Y') === $currentYear) {
                $mIdx = (int) $c->created_at->format('n') - 1;
                $monthlyRevenue[$mIdx] += ($c->jumlah / 1000000); // Dalam Juta Rupiah
                $fee = $c->fee_marketplace > 0 ? $c->fee_marketplace : ($c->jumlah * 0.10);
                $monthlyCommission[$mIdx] += ($fee / 1000000);
            }
        }

        foreach (\App\Models\ElearningTransaction::where('payment_status', 'completed')->whereYear('created_at', $currentYear)->get() as $el) {
            if ($el->created_at) {
                $mIdx = (int) $el->created_at->format('n') - 1;
                $monthlyRevenue[$mIdx] += ($el->nominal / 1000000);
                $monthlyCommission[$mIdx] += ($el->nominal / 1000000);
            }
        }

        foreach ($paidContests as $pc) {
            if ($pc->created_at && (int) $pc->created_at->format('Y') === $currentYear && $pc->contest && $pc->contest->biaya_pendaftaran > 0) {
                $mIdx = (int) $pc->created_at->format('n') - 1;
                $monthlyRevenue[$mIdx] += ($pc->contest->biaya_pendaftaran / 1000000);
                $monthlyCommission[$mIdx] += ($pc->contest->biaya_pendaftaran / 1000000);
            }
        }

        $totalYearRevenue = array_sum($monthlyRevenue);
        $avgMonthlyRevenue = round($totalYearRevenue / 12, 1);
        $maxMonthlyRevenue = round(max($monthlyRevenue), 1);
        $totalCommission = round(array_sum($monthlyCommission), 1);

        // 4. Category Demand Breakdown (100% Dynamic from Contracts & Categories)
        $categories = Category::all();
        $totalBookings = Contract::whereIn('status_contract', ['active', 'completed'])->count();
        $palette = ['bg-amber-400', 'bg-gray-800 dark:bg-gray-200', 'bg-amber-600', 'bg-gray-500', 'bg-gray-400'];
        $categoryStats = [];

        foreach ($categories as $cat) {
            $catCount = Contract::whereIn('status_contract', ['active', 'completed'])
                ->whereHas('photographer.categories', function ($q) use ($cat) {
                    $q->where('categories.id', $cat->id);
                })->count();

            $share = $totalBookings > 0 ? round(($catCount / $totalBookings) * 100) : 0;
            $categoryStats[] = [
                'name' => $cat->nama_kategori,
                'share' => $share,
                'count' => $catCount,
            ];
        }

        // Urutkan berdasarkan jumlah pesanan terbanyak secara dinamis
        usort($categoryStats, fn($a, $b) => $b['count'] <=> $a['count']);

        // Berikan warna palet setelah pengurutan
        foreach ($categoryStats as $idx => &$cStat) {
            $cStat['color'] = $palette[$idx % count($palette)];
        }
        unset($cStat);

        $topCategory = (!empty($categoryStats) && $categoryStats[0]['count'] > 0) ? $categoryStats[0] : null;

        // 5. Top 4 Performing Photographers
        $topPhotographers = RolePhotographer::with([
            'user', 'province', 'city', 'categories',
            'testimonials' => fn($q) => $q->where('is_hidden', false),
        ])
            ->withCount(['contracts as completed_orders_count' => function ($q) {
                $q->where('status_contract', 'completed')
                  ->where('is_validated_client', true);
            }])
            ->orderByDesc('completed_orders_count')
            ->take(4)
            ->get();

        // 6. Recent 6 Transactions for Dashboard Preview Table (Rule #94 Dynamic & Deterministic Sort)
        $recentTransactions = Contract::with([
            'client.user',
            'photographer.user',
            'bookingDetail.service',
            'payments',
            'cancellation'
        ])
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->take(6)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalClients',
            'totalPhotographers',
            'verifiedPhotographers',
            'totalContracts',
            'grossRevenue',
            'platformFee',
            'escrowHolding',
            'pendingVerifications',
            'pendingPayouts',
            'pendingContestReviews',
            'months',
            'monthlyRevenue',
            'monthlyCommission',
            'avgMonthlyRevenue',
            'maxMonthlyRevenue',
            'totalCommission',
            'categoryStats',
            'topCategory',
            'topPhotographers',
            'recentTransactions'
        ));
    }
}