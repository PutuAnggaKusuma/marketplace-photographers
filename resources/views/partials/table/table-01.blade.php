@php
  $contractsList = isset($recentTransactions) && $recentTransactions->isNotEmpty() 
    ? $recentTransactions 
    : \App\Models\Contract::with(['client.user', 'photographer.user', 'photographer.categories', 'bookingDetail.service', 'payments'])->orderBy('created_at', 'desc')->take(6)->get();
@endphp

<div class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-3 pt-4 dark:border-gray-800 dark:bg-white/[0.03] sm:px-6">
  <div class="flex flex-col gap-2 mb-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <h3 class="text-base sm:text-lg font-bold text-gray-800 dark:text-white/90">
        Transaksi Booking & Verifikasi Terkini
      </h3>
      <p class="text-xs text-gray-400 mt-0.5">Monitoring pemesanan sesi foto & status escrow real-time</p>
    </div>

    <div class="flex items-center gap-2">
      <a href="{{ auth()->user() && auth()->user()->role === 'photographer' ? route('photographer.bookings') : route('admin.transactions') }}"
        class="text-xs font-extrabold text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 hover:underline inline-flex items-center gap-1 transition">
        <span>Lihat Semua</span>
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
      </a>
    </div>
  </div>

  <div class="w-full overflow-x-auto">
    <table class="min-w-full text-left">
      <thead>
        <tr class="border-gray-100 border-y dark:border-gray-800 text-[11px] font-extrabold uppercase tracking-wider text-gray-500 dark:text-gray-400 bg-gray-50/50 dark:bg-gray-900/40">
          <th class="py-3 px-3 sm:px-4">Paket & Fotografer</th>
          <th class="py-3 px-3 sm:px-4">Kategori Sesi</th>
          <th class="py-3 px-3 sm:px-4">Total Biaya</th>
          <th class="py-3 px-3 sm:px-4">Status Transaksi</th>
        </tr>
      </thead>

      <tbody class="divide-y divide-gray-100 dark:divide-gray-800 text-xs">
        @forelse($contractsList as $item)
          @php
            $service = $item->bookingDetail->service ?? null;
            $serviceName = $service ? $service->nama_layanan : 'Paket Sesi Foto Eksklusif';
            
            $catName = 'Dokumentasi & Sesi Foto';
            if ($item->photographer && $item->photographer->categories && $item->photographer->categories->isNotEmpty()) {
                $catName = $item->photographer->categories->first()->nama_kategori;
            }

            $photoImg = ($item->photographer && $item->photographer->foto) 
                ? $item->photographer->foto 
                : 'https://images.unsplash.com/photo-1519741497674-611481863552?w=150&q=80';
          @endphp
          <tr class="hover:bg-amber-50/30 dark:hover:bg-amber-950/20 transition">
            <!-- Paket & Fotografer -->
            <td class="py-3.5 px-3 sm:px-4">
              <div class="flex items-center gap-3 min-w-[200px]">
                <div class="h-11 w-11 overflow-hidden rounded-xl shrink-0 border border-gray-100 dark:border-gray-700 shadow-2xs">
                  <img src="{{ $photoImg }}" class="w-full h-full object-cover" alt="{{ $serviceName }}" />
                </div>
                <div class="min-w-0">
                  <p class="font-extrabold text-gray-900 dark:text-white truncate max-w-[220px]">
                    {{ $serviceName }}
                  </p>
                  <p class="text-[11px] text-gray-500 dark:text-gray-400 truncate max-w-[220px]">
                    Fotografer: <span class="font-bold text-gray-700 dark:text-gray-300">{{ $item->photographer->nama ?? 'Studio' }}</span> • Klien: {{ $item->client->nama ?? 'Klien' }}
                  </p>
                </div>
              </div>
            </td>

            <!-- Kategori Sesi (Plain Text without Card Badge) -->
            <td class="py-3.5 px-3 sm:px-4 whitespace-nowrap">
              <p class="font-medium text-gray-700 text-theme-sm dark:text-gray-300">
                {{ $catName }}
              </p>
            </td>

            <!-- Total Biaya -->
            <td class="py-3.5 px-3 sm:px-4 whitespace-nowrap">
              <p class="font-extrabold text-gray-900 dark:text-white text-theme-sm">
                Rp {{ number_format($item->jumlah, 0, ',', '.') }}
              </p>
            </td>

            <!-- Status Transaksi & Escrow -->
            <td class="py-3.5 px-3 sm:px-4 whitespace-nowrap">
              @if($item->status_payout === 'paid' || $item->status_contract === 'completed')
                <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">
                  Payout Dicairkan
                </span>
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
              @else
                <span class="text-xs font-bold text-amber-600 dark:text-amber-400">
                  Holding Escrow
                </span>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="4" class="py-8 text-center text-gray-400 font-bold text-xs">
              Belum ada data transaksi booking aktif.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>