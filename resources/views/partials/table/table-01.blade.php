@php
  $contractsList = isset($recentTransactions) && $recentTransactions->isNotEmpty() 
    ? $recentTransactions 
    : \App\Models\Contract::with(['client.user', 'photographer.user', 'bookingDetail.service', 'payments'])
        ->orderBy('created_at', 'desc')
        ->orderBy('id', 'desc')
        ->take(6)
        ->get();
  $totalContractsCount = $totalContracts ?? \App\Models\Contract::count();
@endphp

<div class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-3 pt-4 dark:border-gray-800 dark:bg-white/[0.03] sm:px-6">
  <div class="flex flex-col gap-2 mb-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <h3 class="text-base sm:text-lg font-bold text-gray-800 dark:text-white/90">
        Transaksi Booking Terkini
      </h3>
      <p class="text-xs text-gray-400 mt-0.5">Monitoring pemesanan sesi foto dan pembayaran terbaru</p>
    </div>

    @if($totalContractsCount > 6)
      <div class="flex items-center gap-2">
        <a href="{{ auth()->user() && auth()->user()->role === 'photographer' ? route('photographer.bookings') : route('admin.transactions') }}"
          class="text-xs font-extrabold text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 hover:underline inline-flex items-center gap-1 transition">
          <span>Lihat Semua</span>
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
        </a>
      </div>
    @endif
  </div>

  <div class="w-full overflow-x-auto">
    <table class="min-w-full text-left">
      <thead>
        <tr class="border-gray-100 border-y dark:border-gray-800 text-[11px] font-extrabold uppercase tracking-wider text-gray-500 dark:text-gray-400 bg-gray-50/50 dark:bg-gray-900/40">
          <th class="py-3.5 px-3 sm:px-4">Invoice</th>
          <th class="py-3.5 px-3 sm:px-4">Klien</th>
          <th class="py-3.5 px-3 sm:px-4">Fotografer</th>
          <th class="py-3.5 px-3 sm:px-4">Rincian Nominal</th>
          <th class="py-3.5 px-3 sm:px-4">Status Pembayaran</th>
          <th class="py-3.5 px-3 sm:px-4 text-center">Aksi</th>
        </tr>
      </thead>

      <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-xs">
        @forelse($contractsList as $item)
          @php
            $invoiceCode = $item->invoice_code;
            $feeAmount = $item->fee_marketplace > 0 ? $item->fee_marketplace : ($item->jumlah * 0.10);
            $netPayout = $item->payout_amount > 0 ? $item->payout_amount : ($item->jumlah - $feeAmount);
            $service = $item->bookingDetail->service ?? null;
            $serviceName = $service ? $service->nama_layanan : ($item->bookingDetail->service_name ?? 'Paket Foto Custom');
          @endphp
          <tr class="hover:bg-amber-50/30 dark:hover:bg-amber-950/20 transition">
            <!-- Invoice -->
            <td class="py-3.5 px-3 sm:px-4 align-middle whitespace-nowrap">
              <div class="font-extrabold text-gray-900 dark:text-white">{{ $invoiceCode }}</div>
              <div class="text-xs text-gray-400 mt-0.5">{{ $item->created_at ? $item->created_at->format('d M Y, H:i') : '-' }}</div>
            </td>

            <!-- Klien -->
            <td class="py-3.5 px-3 sm:px-4 whitespace-nowrap">
              <div class="font-bold text-gray-900 dark:text-white">{{ $item->client->nama ?? 'Klien' }}</div>
              <div class="text-xs text-gray-500 dark:text-gray-400">{{ $item->client->user->email ?? '-' }}</div>
            </td>

            <!-- Fotografer -->
            <td class="py-3.5 px-3 sm:px-4 whitespace-nowrap">
              <div class="font-bold text-gray-900 dark:text-white">{{ $item->photographer->nama ?? 'Fotografer' }}</div>
              <div class="text-xs text-gray-500 dark:text-gray-400 max-w-[200px] truncate">
                {{ $serviceName }}
              </div>
            </td>

            <!-- Rincian Nominal -->
            <td class="py-3.5 px-3 sm:px-4 space-y-0.5 whitespace-nowrap">
              <div class="font-extrabold text-gray-900 dark:text-white">Total: Rp {{ number_format($item->jumlah, 0, ',', '.') }}</div>
              <div class="text-xs text-gray-500">
                Komisi Platform (10%): <span class="font-bold text-gray-700 dark:text-gray-300">Rp {{ number_format($feeAmount, 0, ',', '.') }}</span>
              </div>
              <div class="text-xs font-bold text-gray-900 dark:text-white">
                Pendapatan Bersih: Rp {{ number_format($netPayout, 0, ',', '.') }}
              </div>
            </td>

            <!-- Status Pembayaran -->
            <td class="py-3.5 px-3 sm:px-4 align-middle whitespace-nowrap">
              @if($item->status_payout === 'paid')
                <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">
                  Sudah Ditransfer
                </span>
                @if($item->payout_at)
                  <p class="text-xs text-gray-400 mt-0.5">{{ $item->payout_at->format('d M Y, H:i') }}</p>
                @endif
              @elseif($item->status_payout === 'ready')
                <span class="text-xs font-bold text-blue-600 dark:text-blue-400">
                  Siap Dicairkan
                </span>
              @elseif($item->status_payout === 'refunded')
                <span class="text-xs font-bold text-rose-600 dark:text-rose-400">
                  Refund
                </span>
              @elseif($item->status_contract === 'pending_payment')
                <span class="text-xs font-bold text-amber-600 dark:text-amber-400">
                  Menunggu Pembayaran
                </span>
              @elseif($item->status_contract === 'cancelled')
                <span class="text-xs font-bold text-gray-400 dark:text-gray-500">
                  Dibatalkan
                </span>
              @else
                <span class="text-xs font-bold text-amber-600 dark:text-amber-400">
                  Dana Ditahan
                </span>
              @endif
            </td>

            <!-- Aksi -->
            <td class="py-3.5 px-3 sm:px-4 align-middle text-center whitespace-nowrap">
              <a href="{{ route('admin.transactions', ['search' => $invoiceCode]) }}" 
                 class="px-3 py-1.5 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200/80 dark:border-amber-900/60 font-bold text-xs transition inline-block">
                Lihat
              </a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="py-8 text-center text-gray-400 font-bold text-xs">
              Belum ada data transaksi booking aktif.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
