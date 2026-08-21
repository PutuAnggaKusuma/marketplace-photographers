<?php

namespace App\Http\Controllers\Photographer;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\RolePhotographer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Tampilkan Halaman Laporan Keuangan & Statistik Studio.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $photographer = RolePhotographer::where('id_user', $user->id)->first();
        if (!$photographer) {
            $photographer = RolePhotographer::first();
        }

        $statusFilter = $request->query('status', 'all');
        $selectedYear = $request->query('year', date('Y'));

        $query = Contract::where('id_photographer', $photographer->id)
            ->with(['client.user', 'bookingDetail.service', 'payments'])
            ->orderBy('created_at', 'desc');

        if ($statusFilter === 'paid') {
            $query->where('status_payout', 'paid');
        } elseif ($statusFilter === 'holding') {
            $query->whereIn('status_payout', ['holding', 'ready']);
        }

        $contracts = $query->paginate(15)->withQueryString();

        // 4 Summary Metrics
        $allContracts = Contract::where('id_photographer', $photographer->id)->get();

        $totalNetIncome = $allContracts->where('status_payout', 'paid')->sum('payout_amount');
        $totalEscrowPending = $allContracts->whereIn('status_payout', ['holding', 'ready'])->sum('payout_amount');
        $completedCount = $allContracts->where('status_contract', 'completed')->count();
        $avgRevenuePerSession = $completedCount > 0 ? ($totalNetIncome / $completedCount) : 0;
        $totalMarketplaceFee = $allContracts->sum('fee_marketplace');

        // Monthly Revenue Calculation for Chart (Current Year)
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $monthlyRevenue = array_fill(1, 12, 0);
        $monthlyBookings = array_fill(1, 12, 0);

        foreach ($allContracts as $c) {
            if ($c->created_at->format('Y') == $selectedYear) {
                $m = (int) $c->created_at->format('n');
                $monthlyRevenue[$m] += $c->payout_amount > 0 ? $c->payout_amount : ($c->jumlah * 0.9);
                $monthlyBookings[$m] += 1;
            }
        }

        $maxMonthlyRevenue = max(array_values($monthlyRevenue)) ?: 1;

        return view('photographer.reports.index', compact(
            'photographer',
            'contracts',
            'statusFilter',
            'selectedYear',
            'totalNetIncome',
            'totalEscrowPending',
            'completedCount',
            'avgRevenuePerSession',
            'totalMarketplaceFee',
            'months',
            'monthlyRevenue',
            'monthlyBookings',
            'maxMonthlyRevenue'
        ));
    }

    /**
     * Tampilkan Halaman Format Cetak Laporan PDF / Print.
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        $photographer = RolePhotographer::where('id_user', $user->id)->first();
        if (!$photographer) {
            $photographer = RolePhotographer::first();
        }

        $selectedYear = $request->query('year', date('Y'));

        $contracts = Contract::where('id_photographer', $photographer->id)
            ->with(['client.user', 'bookingDetail.service', 'payments'])
            ->orderBy('created_at', 'desc')
            ->get();

        $totalNetIncome = $contracts->where('status_payout', 'paid')->sum('payout_amount');
        $totalEscrowPending = $contracts->whereIn('status_payout', ['holding', 'ready'])->sum('payout_amount');
        $totalMarketplaceFee = $contracts->sum('fee_marketplace');
        $totalGrossVolume = $contracts->sum('jumlah');

        return view('photographer.reports.export', compact(
            'photographer',
            'contracts',
            'selectedYear',
            'totalGrossVolume',
            'totalNetIncome',
            'totalEscrowPending',
            'totalMarketplaceFee'
        ));
    }
}