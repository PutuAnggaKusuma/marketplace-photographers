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

        $grossRevenue = Contract::sum('jumlah') + $elearningRevenue + $contestRevenue;
        $platformFee = (Contract::sum('fee_marketplace') ?: (Contract::sum('jumlah') * 0.10)) + $elearningRevenue + $contestRevenue;
        $escrowHolding = Contract::whereIn('status_payout', ['holding', 'ready'])->sum('jumlah');
        
        // 2. Actionable Operational Alerts (100% Dynamic)
        $pendingVerifications = RolePhotographer::where(function ($q) {
            $q->where('status_verifikasi', 'menunggu')
              ->orWhere(function ($q2) {
                  $q2->where('status_verifikasi', 'terverifikasi')
                     ->where('status_etalase', 'menunggu');
              });
        })->count();
        $pendingPayouts = Contract::where('status_payout', 'ready')->count();
        $pendingContestReviews = PhotoContest::where('status', 'pending')->count();

        // 3. Monthly Financial & Booking Trends (Jan - Des 2026)
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $monthlyRevenue = array_fill(0, 12, 0);
        $monthlyCommission = array_fill(0, 12, 0);

        foreach (Contract::all() as $c) {
            if ($c->created_at) {
                $mIdx = (int) $c->created_at->format('n') - 1;
                $monthlyRevenue[$mIdx] += ($c->jumlah / 1000000); // Dalam Juta Rupiah
                $fee = $c->fee_marketplace > 0 ? $c->fee_marketplace : ($c->jumlah * 0.10);
                $monthlyCommission[$mIdx] += ($fee / 1000000);
            }
        }

        foreach (\App\Models\ElearningTransaction::where('payment_status', 'completed')->get() as $el) {
            if ($el->created_at) {
                $mIdx = (int) $el->created_at->format('n') - 1;
                $monthlyRevenue[$mIdx] += ($el->nominal / 1000000);
                $monthlyCommission[$mIdx] += ($el->nominal / 1000000);
            }
        }

        foreach ($paidContests as $pc) {
            if ($pc->created_at && $pc->contest && $pc->contest->biaya_pendaftaran > 0) {
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
        $totalBookings = Contract::count();
        $palette = ['bg-amber-400', 'bg-gray-800 dark:bg-gray-200', 'bg-amber-600', 'bg-gray-500', 'bg-gray-400'];
        $categoryStats = [];

        foreach ($categories as $idx => $cat) {
            $catCount = Contract::whereHas('photographer.categories', function ($q) use ($cat) {
                $q->where('categories.id', $cat->id);
            })->count();

            $share = $totalBookings > 0 ? round(($catCount / $totalBookings) * 100) : 0;
            $categoryStats[] = [
                'name' => $cat->nama_kategori,
                'share' => $share,
                'count' => $catCount,
                'color' => $palette[$idx % count($palette)],
            ];
        }

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
            'topPhotographers'
        ));
    }
}