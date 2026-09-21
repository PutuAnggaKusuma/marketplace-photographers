@extends('layouts.photographer')

@section('title', 'Kelola Order Booking & Kontrak Masuk — Dashboard Fotografer')

@section('content')
<div class="space-y-6 w-full" x-data="{ 
    uploadModal: false, 
    selectedContractId: null, 
    selectedClientName: '',
    searchQuery: '',
    currentStatus: '{{ $filterStatus ?? 'all' }}',
    isLoading: false,
    
    applyFilter(val) {
        this.currentStatus = val;
        let url = new URL(window.location.href);
        url.searchParams.set('status', val);
        url.searchParams.set('page', '1');
        this.fetchTable(url.toString());
    },
    handleSearch(e) {
        let q = e.target.value.toLowerCase();
        let rows = document.querySelectorAll('#booking-table-body tr');
        rows.forEach(r => {
            let text = r.innerText.toLowerCase();
            r.style.display = text.includes(q) ? '' : 'none';
        });
    },
    fetchTable(url) {
        this.isLoading = true;
        fetch(url, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.text())
            .then(html => {
                let parser = new DOMParser();
                let doc = parser.parseFromString(html, 'text/html');
                let newTable = doc.querySelector('#dashboard-table-container');
                let curTable = document.querySelector('#dashboard-table-container');
                if (newTable && curTable) {
                    curTable.innerHTML = newTable.innerHTML;
                }
                history.pushState(null, '', url);
                this.isLoading = false;
            })
            .catch(err => {
                console.error(err);
                this.isLoading = false;
            });
    }
}">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <span class="text-[11px] font-extrabold uppercase tracking-widest text-amber-600 dark:text-amber-400">Manajemen Transaksi & Reservasi</span>
            <h1 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">Kelola Order Booking & Kontrak Masuk</h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Konfirmasi jadwal booking dari klien, tinjau catatan khusus, dan unggah tautan hasil foto setelah sesi selesai</p>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 text-xs text-emerald-800 dark:text-emerald-300 font-bold flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Metric Stat Cards (Strictly Following Rule #7, #8, #9, #18) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        
        <!-- Card 1: Total Order Masuk -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
            <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Total Semua Order</span>
            <div class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">{{ $stats['all'] ?? 0 }} Order</div>
            <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">Akumulasi seluruh pesanan</p>
        </div>

        <!-- Card 2: Menunggu Konfirmasi -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
            <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Perlu Konfirmasi</span>
            <div class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">{{ $stats['pending'] ?? 0 }} Order</div>
            <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">Menunggu respon fotografer</p>
        </div>

        <!-- Card 3: Jadwal Dikonfirmasi -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
            <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Sesi Dikonfirmasi</span>
            <div class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">{{ $stats['confirmed'] ?? 0 }} Order</div>
            <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">Jadwal pemotretan aktif</p>
        </div>

        <!-- Card 4: Selesai -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">
            <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Selesai & Terkirim</span>
            <div class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">{{ $stats['completed'] ?? 0 }} Order</div>
            <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">Hasil foto telah diserahkan</p>
        </div>

    </div>

    <!-- Standard Data Table Card (Following Rules #10, #12, #13, #14, #15, #16, #17, #18) -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm overflow-hidden space-y-4">
        
        <!-- Search Toolbar & Filter (Alpine.js Custom Floating Dropdown on Left, Search Bar on Right) -->
        <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex flex-col md:flex-row items-center justify-between gap-4">
            
            <!-- Filter Status di Kiri -->
            <div class="flex items-center gap-3 w-full md:w-auto">
                @php
                    $statusLabels = [
                        'all' => 'Semua Status Order',
                        'pending' => 'Menunggu Konfirmasi',
                        'confirmed' => 'Jadwal Dikonfirmasi',
                        'completed' => 'Selesai & Dikirim'
                    ];
                    $curStatName = $statusLabels[$filterStatus ?? 'all'] ?? 'Semua Status Order';
                @endphp
                <div class="relative w-full sm:w-auto min-w-[210px]" x-data="{ open: false, selectedId: '{{ $filterStatus ?? 'all' }}', selectedName: '{{ $curStatName }}' }">
                    <button @click="open = !open" type="button" 
                            class="w-full px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs font-bold text-gray-800 dark:text-white flex items-center justify-between gap-2 shadow-2xs hover:border-amber-400 dark:hover:border-amber-500 cursor-pointer transition">
                        <span x-text="selectedName" class="truncate"></span>
                        <svg class="w-3.5 h-3.5 text-gray-400 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" @click.away="open = false" x-transition 
                         class="absolute z-50 left-0 mt-2 min-w-[220px] w-full bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700/80 py-1 max-h-60 overflow-y-auto" style="display: none;">
                        @foreach($statusLabels as $stKey => $stVal)
                            <button type="button" @click="selectedId = '{{ $stKey }}'; selectedName = '{{ $stVal }}'; open = false; applyFilter('{{ $stKey }}')" 
                                    class="w-full px-3.5 py-2 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition flex items-center justify-between text-left cursor-pointer border-b border-gray-50 dark:border-gray-700/40 last:border-0"
                                    :class="selectedId === '{{ $stKey }}' ? 'bg-amber-50/70 dark:bg-amber-950/30 text-amber-600 font-extrabold' : ''">
                                <span>{{ $stVal }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Search di Kanan -->
            <div class="relative w-full md:w-80">
                <input type="text" 
                       @input="handleSearch($event)" 
                       placeholder="Cari nama klien, nomor HP, lokasi..." 
                       class="w-full pl-9 pr-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
        </div>

        <div id="dashboard-table-container" class="transition-opacity duration-200" :class="isLoading ? 'opacity-40 pointer-events-none' : 'opacity-100'">

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-[11px] font-extrabold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                            <th class="py-3.5 px-4 sm:px-6 w-3/12">ID Kontrak & Klien</th>
                            <th class="py-3.5 px-4 sm:px-6 w-3/12">Layanan & Jadwal Sesi</th>
                            <th class="py-3.5 px-4 sm:px-6 w-2/12">Nominal Kontrak</th>
                            <th class="py-3.5 px-4 sm:px-6 w-2/12 text-center">Status Order</th>
                            <th class="py-3.5 px-4 sm:px-6 w-2/12 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="booking-table-body" class="divide-y divide-gray-100 dark:divide-gray-700/60 text-xs">
                        @forelse($contracts as $c)
                            @php
                                $detail = $c->bookingDetail;
                                $client = $c->client;
                            @endphp
                            <tr class="hover:bg-amber-50/30 dark:hover:bg-amber-950/20 transition">
                                
                                <!-- ID Kontrak & Klien -->
                                <td class="py-4 px-4 sm:px-6 align-top">
                                    <div class="space-y-0.5">
                                        <span class="text-[10px] font-mono font-bold text-gray-400">#KTRK-{{ str_pad($c->id, 5, '0', STR_PAD_LEFT) }}</span>
                                        <h4 class="font-black text-gray-900 dark:text-white text-sm tracking-tight">{{ $client->nama ?? 'Klien' }}</h4>
                                        <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">{{ $client->nomor_telepon ?: '-' }}</p>
                                    </div>
                                </td>

                                <!-- Layanan & Jadwal -->
                                <td class="py-4 px-4 sm:px-6 align-top">
                                    <div class="space-y-1">
                                        <span class="font-bold text-gray-800 dark:text-gray-200 block">{{ $detail->service->nama_layanan ?? 'Paket Kustom' }}</span>
                                        <div class="text-[11px] text-gray-500 dark:text-gray-400 space-y-0.5">
                                            <div class="font-medium text-gray-700 dark:text-gray-300">{{ $detail && $detail->booking_date ? \Carbon\Carbon::parse($detail->booking_date)->translatedFormat('d F Y') : '-' }}</div>
                                            <div>{{ $detail && ($detail->jam_mulai || $detail->jam_selesai) ? $detail->jam_mulai . ' - ' . $detail->jam_selesai . ' WITA' : '-' }}</div>
                                            @if($detail && $detail->lokasi)
                                                <div class="text-gray-600 dark:text-gray-300 truncate max-w-xs font-medium">{{ $detail->lokasi }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <!-- Nominal Kontrak (Rp) -->
                                <td class="py-4 px-4 sm:px-6 align-top whitespace-nowrap">
                                    <span class="text-sm font-black text-gray-900 dark:text-white">
                                        Rp {{ number_format($c->jumlah, 0, ',', '.') }}
                                    </span>
                                    <span class="text-[11px] text-gray-400 block mt-0.5">
                                        Net: Rp {{ number_format($c->payout_amount ?: ($c->jumlah * 0.9), 0, ',', '.') }}
                                    </span>
                                </td>

                                <!-- Status (Pure Colored Text - Rule #14) -->
                                <td class="py-4 px-4 sm:px-6 align-top text-center whitespace-nowrap">
                                    @if($c->status_contract === 'completed' || $detail?->status_booking === 'completed')
                                        <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">Selesai & Dikirim</span>
                                    @elseif($c->status_contract === 'active' || $detail?->status_booking === 'confirmed')
                                        <span class="text-xs font-bold text-blue-600 dark:text-blue-400">Dikonfirmasi</span>
                                    @elseif($c->status_contract === 'cancelled')
                                        <span class="text-xs font-bold text-rose-600 dark:text-rose-400">Dibatalkan</span>
                                    @else
                                        <span class="text-xs font-bold text-amber-600 dark:text-amber-400">Menunggu Konfirmasi</span>
                                    @endif
                                </td>

                                <!-- Aksi (Pure Text Buttons) -->
                                <td class="py-4 px-4 sm:px-6 align-top text-center whitespace-nowrap">
                                    <div class="flex flex-col items-center gap-1.5">
                                        @if($detail && $detail->status_booking === 'pending')
                                            <form action="{{ route('photographer.bookings.status', $c->id) }}" method="POST" class="w-full">
                                                @csrf
                                                <input type="hidden" name="status" value="confirmed">
                                                <button type="submit" class="w-full px-3 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/60 font-bold text-xs transition">
                                                    Konfirmasi
                                                </button>
                                            </form>
                                        @elseif($detail && $detail->status_booking === 'confirmed')
                                            <button type="button" @click="selectedContractId = {{ $c->id }}; selectedClientName = '{{ addslashes($client->nama ?? 'Klien') }}'; uploadModal = true" class="w-full px-3 py-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200/80 dark:border-amber-800/60 font-bold text-xs transition">
                                                Kirim Galeri
                                            </button>
                                        @endif

                                        <a href="{{ route('photographer.chat.client', $c->id_client) }}" class="w-full px-3 py-1.5 rounded-lg bg-gray-50 hover:bg-gray-100 text-gray-700 dark:bg-gray-700/60 dark:text-gray-200 border border-gray-200 dark:border-gray-700 font-bold text-xs transition text-center">
                                            Chat Klien
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-gray-400 font-bold text-xs">
                                    Belum ada order booking masuk ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($contracts->total() > 0)
                <div class="p-4 border-t border-gray-200 dark:border-gray-700/80 bg-gray-50/50 dark:bg-gray-900/50">
                    {{ $contracts->links('partials.dashboard.pagination') }}
                </div>
            @endif
        </div>

    </div>

    <!-- Modal Upload Galeri Hasil Foto -->
    <div x-show="uploadModal" 
         @click.away="uploadModal = false" 
         class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4" 
         style="display: none;">
        <div class="bg-white dark:bg-gray-800 max-w-lg w-full p-6 sm:p-8 rounded-3xl border border-gray-200 dark:border-gray-700 shadow-2xl space-y-6">
            
            <div class="space-y-1">
                <span class="text-[10px] font-black uppercase tracking-wider text-amber-600 dark:text-amber-400">Penyerahan Hasil Karya</span>
                <h3 class="text-xl font-black text-gray-900 dark:text-white">Unggah Link Galeri Hasil Foto</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">Kirimkan tautan Google Drive / Cloud Album hasil retouching foto kepada <strong x-text="selectedClientName" class="text-gray-900 dark:text-white"></strong>.</p>
            </div>

            <form :action="'/photographer/bookings/' + selectedContractId + '/gallery'" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Tautan URL Google Drive / Cloud Gallery <span class="text-rose-500">*</span></label>
                    <input type="url" name="hasil_foto_url" placeholder="https://drive.google.com/drive/folders/..." required class="w-full px-4 py-2.5 text-xs text-gray-800 dark:text-white bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-amber-400 focus:outline-none" />
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Pesan / Catatan Tambahan Untuk Klien</label>
                    <textarea name="catatan_fotografer" rows="3" placeholder="Tuliskan ucapan terima kasih atau petunjuk pengunduhan foto..." class="w-full px-4 py-2.5 text-xs text-gray-800 dark:text-white bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-amber-400 focus:outline-none resize-none"></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" @click="uploadModal = false" class="px-5 py-2.5 text-xs font-bold text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white rounded-xl bg-gray-100 dark:bg-gray-700 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs rounded-xl shadow-xs transition cursor-pointer">
                        Kirim Galeri
                    </button>
                </div>
            </form>

        </div>
    </div>

</div>
@endsection