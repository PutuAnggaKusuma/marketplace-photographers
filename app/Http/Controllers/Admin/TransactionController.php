<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Tampilkan Halaman Monitoring Transaksi & Escrow Admin.
     */
        public function index(Request $request)
    {
        $statusFilter = $request->query('status', 'all');
        $sort = $request->query('sort', 'latest');
        $search = $request->query('search', '');

        $query = Contract::with([
            'client.user',
            'photographer.user',
            'bookingDetail.service',
            'payments'
        ]);

        if ($statusFilter !== 'all') {
            $query->where('status_payout', $statusFilter);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('client', function ($cq) use ($search) {
                    $cq->where('nama', 'like', "%{$search}%");
                })->orWhereHas('photographer', function ($pq) use ($search) {
                    $pq->where('nama', 'like', "%{$search}%");
                })->orWhereHas('payments', function ($payq) use ($search) {
                    $payq->where('external_id', 'like', "%{$search}%");
                });
            });
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

        // Calculate Overview Statistics
        $allContracts = Contract::all();
        
        $totalGrossVolume = $allContracts->where('status_payout', '!=', 'refunded')->sum('jumlah');
        $totalEscrowHolding = $allContracts->where('status_payout', 'holding')->sum('payout_amount');
        $totalEscrowReady = $allContracts->where('status_payout', 'ready')->sum('payout_amount');
        $totalPayoutPaid = $allContracts->where('status_payout', 'paid')->sum('payout_amount');
        $totalMarketplaceFee = $allContracts->where('status_payout', '!=', 'refunded')->sum('fee_marketplace');

        return view('admin.transactions.index', compact(
            'contracts',
            'statusFilter',
            'sort',
            'search',
            'totalGrossVolume',
            'totalEscrowHolding',
            'totalEscrowReady',
            'totalPayoutPaid',
            'totalMarketplaceFee'
        ));
    }

    public function releasePayout(Request $request, $id)
    {
        $contract = Contract::with('photographer')->findOrFail($id);

        $request->validate([
            'payout_notes' => 'nullable|string|max:500'
        ]);

        $feeRate = 0.10; // 10%
        $feeMarketplace = $contract->jumlah * $feeRate;
        $payoutAmount = $contract->jumlah - $feeMarketplace;

        $notes = $request->input('payout_notes', 'Pencairan dana payout disetujui Admin.');

        $contract->update([
            'status_payout' => 'paid',
            'fee_marketplace' => $feeMarketplace,
            'payout_amount' => $payoutAmount,
            'payout_at' => now(),
            'payout_notes' => $notes,
        ]);

        $formattedAmount = 'Rp ' . number_format($payoutAmount, 0, ',', '.');
        $photoName = $contract->photographer->nama ?? 'Studio Fotografer';

        
        // Dispatch Notification to Photographer Studio
        if ($contract->photographer && $contract->photographer->id_user) {
            \App\Models\Notification::send(
                $contract->photographer->id_user,
                'Payout Dana Berhasil Dicairkan',
                "Dana Payout sebesar {$formattedAmount} telah dicairkan ke rekening studio Anda.",
                'payout',
                url('/photographer/reports')
            );
        }

        return redirect()->back()->with('success', "Payout sebesar {$formattedAmount} berhasil dicairkan ke {$photoName}!");
    }

    /**
     * Process Refund Dana ke Klien.
     */
    public function processRefund(Request $request, $id)
    {
        $contract = Contract::with('client')->findOrFail($id);

        $request->validate([
            'refund_notes' => 'nullable|string|max:500'
        ]);

        $notes = $request->input('refund_notes', 'Refund dana disetujui Admin.');

        $contract->update([
            'status_payout' => 'refunded',
            'status_contract' => 'cancelled',
            'fee_marketplace' => 0,
            'payout_amount' => 0,
            'payout_notes' => $notes,
        ]);

        $clientName = $contract->client->nama ?? 'Klien';

        return redirect()->back()->with('success', "Dana transaksi berhasil direfund ke akun {$clientName}.");
    }
}