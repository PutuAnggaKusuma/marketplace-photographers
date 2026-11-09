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
        $photographer = RolePhotographer::firstOrCreate(
            ['id_user' => $user->id],
            ['nama' => $user->nama]
        );

        $statusFilter = $request->query('status', 'all');
        $sort = $request->query('sort', 'latest');
        $selectedYear = $request->query('year', date('Y'));

        $query = Contract::where('id_photographer', $photographer->id)
            ->with(['client.user', 'bookingDetail.service', 'payments']);

        if ($statusFilter === 'paid') {
            $query->where('status_payout', 'paid');
        } elseif ($statusFilter === 'holding') {
            $query->whereIn('status_payout', ['holding', 'ready']);
        }

        if ($sort === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } elseif ($sort === 'highest') {
            $query->orderBy('jumlah', 'desc');
        } elseif ($sort === 'lowest') {
            $query->orderBy('jumlah', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $perPage = (int) $request->query('per_page', 10);
        if (!in_array($perPage, [10, 20, 50, 100])) $perPage = 10;
        $contracts = $query->paginate($perPage)->withQueryString();

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
            'sort',
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

    public function export(Request $request)
    {
        $user = Auth::user();
        $photographer = RolePhotographer::firstOrCreate(
            ['id_user' => $user->id],
            ['nama' => $user->nama]
        );

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

    /**
     * Ajukan Pencairan Dana Payout (90%) ke Rekening Fotografer.
     */
    public function requestPayout(Request $request, $id)
    {
        $user = Auth::user();
        $photographer = RolePhotographer::firstOrCreate(
            ['id_user' => $user->id],
            ['nama' => $user->nama]
        );

        $contract = Contract::where('id_photographer', $photographer->id)->findOrFail($id);

        if ($contract->status_payout !== 'ready') {
            return redirect()->back()->with('error', 'Status pesanan belum siap dicairkan atau dana telah diproses sebelumnya.');
        }

        if (empty($photographer->nama_bank) || empty($photographer->nomor_rekening)) {
            return redirect()->route('photographer.profile')
                ->with('warning', 'Silakan lengkapi informasi Rekening Bank (Nama Bank, Nomor Rekening, Atas Nama) di profil Anda sebelum mengajukan pencairan payout.');
        }

        $feeRate = 0.10;
        $feeMarketplace = $contract->jumlah * $feeRate;
        $netPayout = $contract->jumlah - $feeMarketplace;
        $formattedAmount = 'Rp ' . number_format($netPayout, 0, ',', '.');
        $contractCode = 'KTRK-' . str_pad($contract->id, 5, '0', STR_PAD_LEFT);

        $contract->update([
            'payout_requested_at' => now(),
            'fee_marketplace' => $feeMarketplace,
            'payout_amount' => $netPayout,
            'payout_notes' => "Diajukan oleh Fotografer pada " . now()->format('d M Y, H:i') . " WIB ke {$photographer->nama_bank} ({$photographer->nomor_rekening}) a.n {$photographer->atas_nama_rekening}.",
        ]);

        // Notifikasi ke seluruh Admin & Super Admin
        $admins = \App\Models\User::whereIn('role', ['super_admin', 'admin'])->get();
        foreach ($admins as $adm) {
            \App\Models\Notification::send(
                $adm->id,
                'Pengajuan Pencairan Payout Baru',
                "Fotografer {$photographer->nama} mengajukan pencairan payout sebesar {$formattedAmount} untuk sesi {$contractCode}.",
                'payout_request',
                route('admin.transactions', ['search' => $contractCode])
            );
        }

        // Catat ke Activity Log
        \App\Models\ActivityLog::record(
            'payout_requested',
            "Fotografer {$photographer->nama} mengajukan pencairan dana payout sebesar {$formattedAmount} untuk sesi {$contractCode}",
            $contract,
            [
                'payout_amount' => $netPayout,
                'bank_name' => $photographer->nama_bank,
                'bank_account' => $photographer->nomor_rekening,
                'bank_holder' => $photographer->atas_nama_rekening,
            ]
        );

        return redirect()->back()->with('success', "Permintaan pencairan payout sebesar {$formattedAmount} berhasil diajukan ke Admin!");
    }
}