@extends('layouts.photographer')







@section('title', 'Kelola Order Booking & Kontrak Masuk — Dashboard Fotografer')







@section('content')



<div class="space-y-6 w-full" x-data="{ 



    uploadModal: false, 



    selectedContractId: null, 



    selectedClientName: '',



    cancelModal: false,



    cancelContractId: null,



    cancelClientName: '',



    rescheduleModal: false,



    rescheduleContractId: null,



    rescheduleClientName: '',



    rescheduleDefaultDate: '{{ date('Y-m-d', strtotime('+1 day')) }}',



    rateClientModal: false,



    rateContractId: null,



    rateClientName: '',



    rateRating: 5,



    rateUlasan: '',



    rateKetepatanWaktu: 5,



    rateKomunikasi: 5,



    rateKerjasama: 5,



    openRateModal(contractId, clientName, rating = 5, ulasan = '', waktu = 5, kom = 5, sama = 5) {



        this.rateContractId = contractId;



        this.rateClientName = clientName;



        this.rateRating = rating || 5;



        this.rateUlasan = ulasan || '';



        this.rateKetepatanWaktu = waktu || 5;



        this.rateKomunikasi = kom || 5;



        this.rateKerjasama = sama || 5;



        this.rateClientModal = true;



    },



    rateFormError: '',

    closeRateModal() {



        this.rateClientModal = false;
        this.rateFormError = '';



    },

    submitRateClient(e) {
        const ratings = [this.rateRating, this.rateKetepatanWaktu, this.rateKomunikasi, this.rateKerjasama];
        if (ratings.some(r => !r || Number(r) < 1 || Number(r) > 5)) {
            e.preventDefault();
            this.rateFormError = 'Semua aspek penilaian wajib dipilih antara 1 sampai 5.';
            return false;
        }
        this.rateFormError = '';
        return true;
    },



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



            <h1 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">Kelola Booking</h1>



        </div>



    </div>







    <!-- Pengingat Sesi Pintar H-1 & Hari-H (Smart Reminder & Countdown) -->



    @include('partials.shared.upcoming-session-banner', ['urgentSession' => $urgentSession, 'role' => 'photographer'])







    <!-- Metric Stat Cards (Strictly Following Rule #7, #8, #9, #18) -->



    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">



        



        <!-- Card 1: Total Order Masuk -->



        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">



            <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Total Semua Order</span>



            <div class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">{{ $stats['all'] ?? 0 }} Order</div>



        </div>







        <!-- Card 2: Menunggu Konfirmasi -->



        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">



            <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Perlu Konfirmasi</span>



            <div class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">{{ $stats['pending'] ?? 0 }} Order</div>



        </div>







        <!-- Card 3: Jadwal Dikonfirmasi -->



        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">



            <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Sesi Dikonfirmasi</span>



            <div class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">{{ $stats['confirmed'] ?? 0 }} Order</div>



        </div>







        <!-- Card 4: Selesai -->



        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-2">



            <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Selesai</span>



            <div class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">{{ $stats['completed'] ?? 0 }} Order</div>



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



                       class="w-full pl-4 pr-10 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition">



                <svg class="w-4 h-4 text-gray-400 absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>



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



                                        <span class="text-[10px] font-mono font-bold text-gray-400">KTRK-{{ str_pad($c->id, 5, '0', STR_PAD_LEFT) }}</span>



                                        <h4 class="font-black text-gray-900 dark:text-white text-sm tracking-tight">{{ $client->nama ?? 'Klien' }}</h4>



                                        <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">{{ $client->nomor_telepon ?: '-' }}</p>

                                        @if($client && $client->receivedPhotographerReviews && $client->receivedPhotographerReviews->count() > 0)

                                            <div class="flex items-center gap-1 text-[10px] font-extrabold text-amber-600 dark:text-amber-400 pt-0.5" title="Reputasi Klien berdasarkan {{ $client->receivedPhotographerReviews->count() }} penilaian fotografer">

                                                <svg class="w-3 h-3 fill-amber-400 text-amber-400 shrink-0" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>

                                                <span>{{ $client->averagePhotographerRating }} ({{ $client->receivedPhotographerReviews->count() }} ulasan)</span>

                                            </div>

                                        @endif

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







                                        <!-- Reschedule Status Banner -->



                                        @if($detail && $detail->reschedule_status === 'pending')



                                            @if($detail->reschedule_by === 'photographer')



                                                <div class="mt-2 p-2 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200/80 dark:border-amber-900/60 text-amber-800 dark:text-amber-300 text-[11px] font-medium space-y-0.5">



                                                    <span class="font-bold block text-amber-900 dark:text-amber-200">Pengajuan Jadwal Ulang Dikirim ke Klien</span>



                                                    <span class="block text-[10px] text-gray-600 dark:text-gray-400">Jadwal baru: {{ \Carbon\Carbon::parse($detail->reschedule_date)->translatedFormat('d F Y') }} • {{ substr($detail->reschedule_time, 0, 5) }} WIB</span>



                                                    <span class="block text-[10px] text-gray-500 italic">"{{ $detail->reschedule_reason }}"</span>



                                                </div>



                                            @elseif($detail->reschedule_by === 'client')



                                                <div class="mt-2 p-2.5 rounded-xl bg-blue-50 dark:bg-blue-950/40 border border-blue-200/80 dark:border-blue-900/60 text-blue-900 dark:text-blue-300 text-[11px] space-y-1.5">



                                                    <div>



                                                        <span class="font-bold block text-blue-900 dark:text-blue-200">Klien Mengajukan Jadwal Baru:</span>



                                                        <span class="font-black text-gray-900 dark:text-white block mt-0.5">{{ \Carbon\Carbon::parse($detail->reschedule_date)->translatedFormat('d F Y') }} • {{ substr($detail->reschedule_time, 0, 5) }} WIB</span>



                                                        <span class="text-[10px] text-gray-600 dark:text-gray-400 block mt-0.5">Alasan: "{{ $detail->reschedule_reason }}"</span>



                                                    </div>



                                                    <div class="flex items-center gap-2 pt-1">



                                                        <form action="{{ route('photographer.bookings.reschedule-respond', $c->id) }}" method="POST" class="inline">



                                                            @csrf



                                                            <input type="hidden" name="action" value="accept">



                                                            <button type="submit" class="px-3 py-1 bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-[10px] rounded-lg shadow-2xs transition cursor-pointer">



                                                                Setujui Jadwal Baru



                                                            </button>



                                                        </form>



                                                        <form action="{{ route('photographer.bookings.reschedule-respond', $c->id) }}" method="POST" class="inline">



                                                            @csrf



                                                            <input type="hidden" name="action" value="reject">



                                                            <button type="submit" class="px-3 py-1 bg-white hover:bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 font-bold text-[10px] rounded-lg transition cursor-pointer">



                                                                Tolak



                                                            </button>



                                                        </form>



                                                    </div>



                                                </div>



                                            @endif



                                        @endif



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
                                    @php
                                        $hasPhotos = !empty($detail?->hasil_foto_url);
                                        $isValidated = (bool) $c->is_validated_client;
                                    @endphp

                                    @if($c->status_contract === 'cancelled')
                                        <span class="text-xs font-bold text-rose-600 dark:text-rose-400">Dibatalkan</span>
                                    @elseif($c->status_contract === 'completed' && $isValidated)
                                        <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">Selesai</span>
                                    @elseif($hasPhotos && !$isValidated)
                                        <span class="text-xs font-bold text-blue-600 dark:text-blue-400">Menunggu Konfirmasi Klien</span>
                                    @elseif($c->status_contract === 'active' || $detail?->status_booking === 'confirmed')
                                        <span class="text-xs font-bold text-blue-600 dark:text-blue-400">Dikonfirmasi</span>
                                    @else
                                        <span class="text-xs font-bold text-amber-500 dark:text-amber-400">Menunggu Konfirmasi</span>
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



                                            <button type="button" 



                                                    @click="cancelContractId = {{ $c->id }}; cancelClientName = '{{ addslashes($client->nama ?? 'Klien') }}'; cancelModal = true" 



                                                    class="w-full px-3 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200/80 dark:border-rose-900/60 font-bold text-xs transition text-center cursor-pointer">



                                                Tolak Sesi



                                            </button>



                                        @elseif($detail && $detail->status_booking === 'confirmed')



                                            <button type="button" @click="selectedContractId = {{ $c->id }}; selectedClientName = '{{ addslashes($client->nama ?? 'Klien') }}'; uploadModal = true" class="w-full px-3 py-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200/80 dark:border-amber-800/60 font-bold text-xs transition">



                                                Simpan Data



                                            </button>



                                            <button type="button" 



                                                    @click="cancelContractId = {{ $c->id }}; cancelClientName = '{{ addslashes($client->nama ?? 'Klien') }}'; cancelModal = true" 



                                                    class="w-full px-3 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200/80 dark:border-rose-900/60 font-bold text-xs transition text-center cursor-pointer">



                                                Batalkan Sesi



                                            </button>



                                        @elseif($c->status_contract === 'completed' || ($detail && $detail->status_booking === 'completed'))



                                            @if($detail && $detail->hasil_foto_url)



                                                <a href="{{ $detail->hasil_foto_url }}" target="_blank" class="w-full px-3 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200/80 font-bold text-xs transition text-center block">



                                                    Buka Link Drive



                                                </a>



                                                <button type="button" @click="selectedContractId = {{ $c->id }}; selectedClientName = '{{ addslashes($client->nama ?? 'Klien') }}'; uploadModal = true" class="w-full px-3 py-1.5 rounded-lg bg-gray-50 hover:bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200 border border-gray-200 dark:border-gray-700 font-bold text-xs transition text-center cursor-pointer">



                                                    Perbarui Link

                                                </button>

                                            @endif



                                            @if($c->photographerClientReview)

                                                <button type="button" 

                                                        @click="openRateModal({{ $c->id }}, '{{ addslashes($client->nama ?? 'Klien') }}', {{ $c->photographerClientReview->rating }}, '{{ addslashes($c->photographerClientReview->ulasan ?? '') }}', {{ $c->photographerClientReview->ketepatan_waktu }}, {{ $c->photographerClientReview->komunikasi }}, {{ $c->photographerClientReview->kerjasama }})" 

                                                        class="w-full px-3 py-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200/80 dark:border-amber-900/60 font-bold text-xs transition text-center cursor-pointer flex items-center justify-center gap-1">

                                                    <svg class="w-3.5 h-3.5 text-amber-500 fill-amber-400 shrink-0" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>

                                                    <span>Ubah Nilai Klien ({{ $c->photographerClientReview->rating }}/5)</span>

                                                </button>

                                            @else

                                                <button type="button" 

                                                        @click="openRateModal({{ $c->id }}, '{{ addslashes($client->nama ?? 'Klien') }}', 5, '', 5, 5, 5)" 

                                                        class="w-full px-3 py-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200/80 dark:border-amber-900/60 font-bold text-xs transition text-center cursor-pointer flex items-center justify-center gap-1">

                                                    <svg class="w-3.5 h-3.5 text-amber-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>

                                                    <span>Beri Nilai Klien</span>

                                                </button>

                                            @endif

                                        @endif







                                        @if($detail && !in_array($c->status_contract, ['completed', 'cancelled']) && $detail->reschedule_status !== 'pending')



                                            <button type="button" 



                                                    @click="rescheduleContractId = {{ $c->id }}; rescheduleClientName = '{{ addslashes($client->nama ?? 'Klien') }}'; rescheduleModal = true" 



                                                    class="w-full px-3 py-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200/80 dark:border-amber-900/60 font-bold text-xs transition text-center cursor-pointer">



                                                Ajukan Jadwal Ulang



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



             



             <!-- Modal Title (No Icon, Centered) -->



             <div class="space-y-1 text-center">



                 


                 <h3 class="text-xl font-black text-gray-900 dark:text-white">Unggah Link Galeri Hasil Foto</h3>



                 <p class="text-xs text-gray-500 dark:text-gray-400">Kirimkan tautan Google Drive / Cloud Album hasil retouching foto kepada <strong x-text="selectedClientName" class="text-gray-900 dark:text-white"></strong>.</p>



             </div>







             <form :action="'/photographer/bookings/' + selectedContractId + '/gallery'" method="POST"
                       x-data="{
                         url: '',
                         touched: false,
                         error: '',
                         validateUrl() {
                           if (!this.url || !this.url.trim()) {
                             this.error = 'Tautan URL album foto wajib diisi.';
                             return false;
                           }
                           try {
                             new URL(this.url);
                           } catch (_) {
                             this.error = 'Format tautan URL tidak valid (harus diawali http:// atau https://).';
                             return false;
                           }
                           this.error = '';
                           return true;
                         },
                         submitGallery(e) {
                           this.touched = true;
                           if (!this.validateUrl()) {
                             e.preventDefault();
                             if (this.$refs.urlInput) {
                               this.$refs.urlInput.focus();
                               this.$refs.urlInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                             }
                             return false;
                           }
                           return true;
                         }
                       }"
                       @submit="submitGallery($event)"
                       novalidate
                       class="space-y-4">
                 @csrf
                 <div>
                     <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Tautan URL Google Drive / Cloud Gallery <span class="text-rose-500">*</span></label>
                     <input type="url" name="hasil_foto_url" x-ref="urlInput" x-model="url" @input="touched = true; validateUrl()" @blur="touched = true; validateUrl()" placeholder="https://drive.google.com/drive/folders/..." :class="touched && error ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-400'" class="w-full px-4 py-2.5 text-xs text-gray-800 dark:text-white bg-gray-50 dark:bg-gray-900 rounded-xl border focus:ring-2 focus:outline-none" />
                     <p x-show="touched && error" x-text="error" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
                 </div>







                 <div>



                     <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">Pesan / Catatan Tambahan Untuk Klien</label>



                     <textarea name="catatan_fotografer" rows="3" placeholder="Tuliskan ucapan terima kasih atau petunjuk pengunduhan foto..." class="w-full px-4 py-2.5 text-xs text-gray-800 dark:text-white bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-amber-400 focus:outline-none resize-none"></textarea>



                 </div>







                 <div class="flex items-center justify-center gap-3 pt-2">



                     <button type="button" @click="uploadModal = false" class="px-5 py-2.5 text-xs font-bold text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white rounded-xl bg-gray-100 dark:bg-gray-700 transition cursor-pointer">



                         Batal



                     </button>



                     <button type="submit" class="px-6 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs rounded-xl shadow-xs transition cursor-pointer">



                         Simpan Data



                     </button>



                 </div>



             </form>







        </div>



    </div>







    <!-- Modal Tolak / Batalkan Sesi Fotografer (Mematuhi Aturan #59) -->



    <div x-show="cancelModal" 



         x-cloak 



         class="fixed inset-0 z-50 overflow-y-auto" 



         aria-labelledby="modal-title" role="dialog" aria-modal="true"



         style="display: none;">



        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">



            <div x-show="cancelModal" 



                 x-transition:enter="ease-out duration-300"



                 x-transition:enter-start="opacity-0"



                 x-transition:enter-end="opacity-100"



                 x-transition:leave="ease-in duration-200"



                 x-transition:leave-start="opacity-100"



                 x-transition:leave-end="opacity-0"



                 @click="cancelModal = false" 



                 class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity"></div>







            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>







            <div x-show="cancelModal" 



                 x-transition:enter="ease-out duration-300"



                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"



                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"



                 x-transition:leave="ease-in duration-200"



                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"



                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"



                 class="relative inline-block align-bottom bg-white dark:bg-gray-850 rounded-2xl p-6 sm:p-7 text-center overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-gray-100 dark:border-gray-700">



                











                <!-- Judul Rata Tengah Tanpa Ikon (Rule #59) -->



                <h3 class="text-lg font-black text-gray-900 dark:text-white mt-1 text-center">



                    Tolak / Batalkan Sesi Foto?



                </h3>







                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 text-center max-w-sm mx-auto">



                    Anda akan menolak atau membatalkan sesi booking dari <strong x-text="cancelClientName" class="text-gray-900 dark:text-white"></strong>. Berikan alasan penolakan secara jelas untuk klien.



                </p>







                <form :action="'/photographer/bookings/' + cancelContractId + '/status'" method="POST"
                      x-data="{
                        alasan: '',
                        touched: false,
                        error: '',
                        validateAlasan() {
                          if (!this.alasan || !this.alasan.trim()) {
                            this.error = 'Alasan penolakan / pembatalan wajib diisi.';
                            return false;
                          }
                          if (this.alasan.trim().length < 5) {
                            this.error = 'Alasan penolakan / pembatalan minimal 5 karakter.';
                            return false;
                          }
                          this.error = '';
                          return true;
                        },
                        submitCancel(e) {
                          this.touched = true;
                          if (!this.validateAlasan()) {
                            e.preventDefault();
                            if (this.$refs.alasanInput) {
                              this.$refs.alasanInput.focus();
                              this.$refs.alasanInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            }
                            return false;
                          }
                          return true;
                        }
                      }"
                      @submit="submitCancel($event)"
                      novalidate
                      class="mt-5 space-y-4">
                    @csrf
                    <input type="hidden" name="status" value="cancelled">
                    
                    <div class="text-left">
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                            Alasan Penolakan / Pembatalan <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="alasan" 
                                  x-ref="alasanInput"
                                  x-model="alasan"
                                  @input="touched = true; validateAlasan()"
                                  @blur="touched = true; validateAlasan()"
                                  rows="3" 
                                  placeholder="Contoh: Jadwal bentrok dengan agenda mendesak, lokasi di luar jangkauan layanan, dll..." 
                                  :class="touched && error ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-400'"
                                  class="w-full px-3.5 py-2.5 text-xs text-gray-800 dark:text-white bg-gray-50 dark:bg-gray-900 border rounded-xl focus:ring-2 focus:outline-none transition"></textarea>
                        <p x-show="touched && error" x-text="error" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
                    </div>







                    <!-- Maksimal 2 Tombol di Bawah (Rule #59) -->



                    <div class="pt-2 flex items-center justify-center gap-3">



                        <button type="button" 



                                @click="cancelModal = false" 



                                class="px-5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 font-bold text-xs hover:bg-gray-100 dark:hover:bg-gray-750 transition cursor-pointer">



                            Batal



                        </button>



                        <button type="submit" 



                                class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-xs transition cursor-pointer">



                            Tolak Sesi



                        </button>



                    </div>



                </form>







            </div>



        </div>



    </div>







    <!-- Modal Ajukan Jadwal Ulang Fotografer (Mematuhi Rule #59, #73, #74, #76) -->



    <div x-show="rescheduleModal" 



         x-cloak 



         class="fixed inset-0 z-50 overflow-y-auto" 



         aria-labelledby="modal-title" role="dialog" aria-modal="true"



         style="display: none;">



        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">



            <div x-show="rescheduleModal" 



                 x-transition:enter="ease-out duration-300"



                 x-transition:enter-start="opacity-0"



                 x-transition:enter-end="opacity-100"



                 x-transition:leave="ease-in duration-200"



                 x-transition:leave-start="opacity-100"



                 x-transition:leave-end="opacity-0"



                 @click="rescheduleModal = false" 



                 class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity"></div>







            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>







            <div x-show="rescheduleModal" 



                 x-transition:enter="ease-out duration-300"



                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"



                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"



                 x-transition:leave="ease-in duration-200"



                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"



                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"



                 class="relative inline-block align-bottom bg-white dark:bg-gray-850 rounded-2xl p-6 sm:p-7 text-center overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-gray-100 dark:border-gray-700">



                



                <!-- Judul Rata Tengah Tanpa Ikon (Rule #59) -->



                <h3 class="text-lg font-black text-gray-900 dark:text-white mt-1 text-center">



                    Ajukan Jadwal Ulang Sesi



                </h3>







                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 text-center max-w-sm mx-auto">



                    Pilih tanggal dan jam mulai pengganti untuk sesi bersama <strong x-text="rescheduleClientName" class="text-gray-900 dark:text-white"></strong>.



                </p>







                <form :action="'/photographer/bookings/' + rescheduleContractId + '/reschedule'" method="POST"
                      x-data="{
                        rescheduleDate: rescheduleDefaultDate,
                        rescheduleReason: '',
                        touched: {
                          date: false,
                          reason: false
                        },
                        errors: {
                          date: '',
                          reason: ''
                        },
                        validateDate() {
                          if (!this.rescheduleDate) {
                            this.errors.date = 'Tanggal baru wajib dipilih.';
                            return false;
                          }
                          if (this.rescheduleDate < '{{ date('Y-m-d') }}') {
                            this.errors.date = 'Tanggal baru minimal hari ini atau mendatang.';
                            return false;
                          }
                          this.errors.date = '';
                          return true;
                        },
                        validateReason() {
                          if (!this.rescheduleReason || !this.rescheduleReason.trim()) {
                            this.errors.reason = 'Alasan perubahan jadwal wajib diisi.';
                            return false;
                          }
                          if (this.rescheduleReason.trim().length < 5) {
                            this.errors.reason = 'Alasan perubahan jadwal minimal 5 karakter.';
                            return false;
                          }
                          this.errors.reason = '';
                          return true;
                        },
                        submitReschedule(e) {
                          this.touched.date = true;
                          this.touched.reason = true;
                          const dOk = this.validateDate();
                          const rOk = this.validateReason();
                          if (!dOk || !rOk) {
                            e.preventDefault();
                            if (!dOk && this.$refs.rescheduleDateInput) {
                              this.$refs.rescheduleDateInput.focus();
                              this.$refs.rescheduleDateInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            } else if (!rOk && this.$refs.rescheduleReasonInput) {
                              this.$refs.rescheduleReasonInput.focus();
                              this.$refs.rescheduleReasonInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            }
                            return false;
                          }
                          return true;
                        }
                      }"
                      @submit="submitReschedule($event)"
                      novalidate
                      class="mt-5 space-y-4 text-left">
                    @csrf
                    
                    <!-- Grid 2 Kolom untuk Field Pendek (Rule #73) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                                Tanggal Baru <span class="text-rose-500">*</span>
                            </label>
                            <input type="date" 
                                   name="reschedule_date" 
                                   x-ref="rescheduleDateInput"
                                   x-model="rescheduleDate"
                                   @input="touched.date = true; validateDate()"
                                   @blur="touched.date = true; validateDate()"
                                   :class="touched.date && errors.date ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-400'"
                                   class="w-full px-3.5 py-2.5 text-xs text-gray-800 dark:text-white bg-gray-50 dark:bg-gray-900 border rounded-xl focus:ring-2 focus:outline-none transition">
                            <p x-show="touched.date && errors.date" x-text="errors.date" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
                        </div>







                        <div x-data="{

                            openTimeDropdown: false,

                            selectedTime: '16:00',

                            times: {

                                '08:00': '08:00 WITA / WIB (Pagi)',

                                '10:00': '10:00 WITA / WIB (Pagi)',

                                '14:00': '14:00 WITA / WIB (Siang)',

                                '16:00': '16:00 WITA / WIB (Sore)',

                                '19:00': '19:00 WITA / WIB (Malam)'

                            }

                        }">



                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">



                                Jam Mulai <span class="text-rose-500">*</span>



                            </label>



                            <input type="hidden" name="reschedule_time" :value="selectedTime">



                            <div class="relative">



                                <button type="button"

                                        @click="openTimeDropdown = !openTimeDropdown"

                                        class="w-full flex items-center justify-between px-3.5 py-2.5 text-xs text-gray-800 dark:text-white bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-amber-400 focus:outline-none transition font-semibold cursor-pointer">



                                    <span x-text="times[selectedTime] || 'Pilih Jam Mulai'"></span>



                                    <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 shrink-0" :class="{ 'rotate-180': openTimeDropdown }" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">



                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>



                                    </svg>



                                </button>



                                <div x-show="openTimeDropdown"

                                     @click.outside="openTimeDropdown = false"

                                     x-transition

                                     class="absolute z-50 top-full mt-1.5 left-0 w-full bg-white dark:bg-gray-900 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 p-1 space-y-0.5 overflow-hidden"

                                     style="display: none;">



                                    <template x-for="(label, val) in times" :key="val">



                                        <button type="button"

                                                @click="selectedTime = val; openTimeDropdown = false"

                                                class="w-full text-left px-3 py-2 text-xs font-bold rounded-lg text-gray-800 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-700 dark:hover:text-amber-300 transition cursor-pointer"

                                                :class="selectedTime === val ? 'bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 font-black' : ''"

                                                x-text="label">



                                        </button>



                                    </template>



                                </div>



                            </div>



                        </div>



                    </div>







                    <!-- Textarea Alasan (Rule #73) -->



                    <div>



                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">



                            Alasan Perubahan Jadwal <span class="text-rose-500">*</span>



                        </label>



                        <textarea name="reschedule_reason" 
                                  x-ref="rescheduleReasonInput"
                                  x-model="rescheduleReason"
                                  @input="touched.reason = true; validateReason()"
                                  @blur="touched.reason = true; validateReason()"
                                  rows="3" 
                                  placeholder="Jelaskan alasan perubahan jadwal untuk klien (misal: cuaca ekstrem, kendala teknis peralatan, dll)..." 
                                  :class="touched.reason && errors.reason ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-400'"
                                  class="w-full px-3.5 py-2.5 text-xs text-gray-800 dark:text-white bg-gray-50 dark:bg-gray-900 border rounded-xl focus:ring-2 focus:outline-none transition leading-relaxed"></textarea>
                        <p x-show="touched.reason && errors.reason" x-text="errors.reason" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>



                    </div>







                    <!-- Tombol Aksi di Tengah Maksimal 2 Tombol (Rule #59, #74, #76) -->

                    <div class="pt-2 flex items-center justify-center gap-3">



                        <button type="button" 



                                @click="rescheduleModal = false" 



                                class="px-5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 font-bold text-xs hover:bg-gray-100 dark:hover:bg-gray-750 transition cursor-pointer">



                            Batal



                        </button>



                        <button type="submit" 



                                class="px-5 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs shadow-xs transition cursor-pointer">



                            Kirim Permintaan



                        </button>



                    </div>



                </form>







            </div>



        </div>



    </div>







    <!-- Modal Beri Nilai Klien (Two-Way Rating - Rule #59, #73, #74, #76 Compliant) -->



    <div x-show="rateClientModal" 



         x-cloak 



         class="fixed inset-0 z-50 overflow-y-auto"



         aria-labelledby="rate-client-title" role="dialog" aria-modal="true">



        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">



            <!-- Backdrop -->



            <div x-show="rateClientModal"



                 x-transition:enter="ease-out duration-300"



                 x-transition:enter-start="opacity-0"



                 x-transition:enter-end="opacity-100"



                 x-transition:leave="ease-in duration-200"



                 x-transition:leave-start="opacity-100"



                 x-transition:leave-end="opacity-0"



                 @click="closeRateModal()"



                 class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity"></div>







            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>







            <!-- Modal Content -->



            <div x-show="rateClientModal"



                 x-transition:enter="ease-out duration-300"



                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"



                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"



                 x-transition:leave="ease-in duration-200"



                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"



                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"



                 class="relative inline-block align-bottom bg-white dark:bg-gray-850 rounded-2xl p-6 sm:p-7 text-center overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100 dark:border-gray-700">



                



                <!-- Centered Title without Icon (Rule #59) -->



                <h3 class="text-lg font-black text-gray-900 dark:text-white mt-1 text-center" id="rate-client-title">



                    Beri Penilaian Performa Klien



                </h3>







                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5 text-center max-w-sm mx-auto">



                    Evaluasi pengalaman kerja sama Anda bersama klien <strong x-text="rateClientName" class="text-gray-900 dark:text-white"></strong> selama sesi pemotretan.



                </p>







                <form :action="'/photographer/bookings/' + rateContractId + '/rate-client'" method="POST" novalidate @submit="submitRateClient($event)" class="mt-5 space-y-4 text-left">



                    @csrf



                    



                    <!-- Rating Keseluruhan (Bintang 1-5) -->



                    <div class="p-3.5 rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 text-center space-y-2">



                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">



                            Skor Kepuasan Keseluruhan <span class="text-rose-500">*</span>



                        </label>



                        <div class="flex items-center justify-center gap-2">



                            <template x-for="star in [1, 2, 3, 4, 5]" :key="star">



                                <button type="button" 



                                        @click="rateRating = star"



                                        class="p-1 text-2xl transition transform hover:scale-110 cursor-pointer focus:outline-none">



                                    <svg class="w-6 h-6 transition" :class="star <= rateRating ? 'text-amber-400 fill-amber-400' : 'text-gray-300 dark:text-gray-600 fill-gray-200 dark:fill-gray-700'" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>



                                </button>



                            </template>



                        </div>



                        <input type="hidden" name="rating" :value="rateRating">



                        <span class="text-xs font-extrabold text-amber-600 dark:text-amber-400 block" x-text="rateRating + ' dari 5 Bintang'"></span>



                    </div>







                    <!-- 3 Aspek Penilaian (Grid 3 Kolom) -->



                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">



                        <!-- Aspek 1: Ketepatan Waktu -->



                        <div class="p-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 space-y-1.5"

                             x-data="{

                                openDropdown: false,

                                options: {

                                    5: '5 - Sangat Tepat',

                                    4: '4 - Tepat Waktu',

                                    3: '3 - Cukup',

                                    2: '2 - Terlambat',

                                    1: '1 - Sangat Lambat'

                                }

                             }">



                            <label class="block text-[11px] font-bold text-gray-700 dark:text-gray-300">



                                Ketepatan Waktu <span class="text-rose-500">*</span>



                            </label>



                            <input type="hidden" name="ketepatan_waktu" :value="rateKetepatanWaktu">



                            <div class="relative">



                                <button type="button"

                                        @click="openDropdown = !openDropdown"

                                        class="w-full flex items-center justify-between py-1.5 px-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 font-semibold text-gray-800 dark:text-white hover:bg-gray-50 transition cursor-pointer">



                                    <span x-text="options[rateKetepatanWaktu] || 'Pilih Nilai'"></span>



                                    <svg class="w-3.5 h-3.5 text-gray-400 transition-transform duration-200 shrink-0" :class="{ 'rotate-180': openDropdown }" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">



                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>



                                    </svg>



                                </button>



                                <div x-show="openDropdown"

                                     @click.outside="openDropdown = false"

                                     x-transition

                                     class="absolute z-50 top-full mt-1 left-0 w-full bg-white dark:bg-gray-900 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 p-1 space-y-0.5 overflow-hidden"

                                     style="display: none;">



                                    <template x-for="(label, val) in options" :key="val">



                                        <button type="button"

                                                @click="rateKetepatanWaktu = parseInt(val); openDropdown = false"

                                                class="w-full text-left px-2.5 py-1.5 text-xs font-bold rounded text-gray-800 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-700 dark:hover:text-amber-300 transition cursor-pointer"

                                                :class="rateKetepatanWaktu === parseInt(val) ? 'bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 font-black' : ''"

                                                x-text="label">



                                        </button>



                                    </template>



                                </div>



                            </div>



                        </div>







                        <!-- Aspek 2: Komunikasi -->



                        <div class="p-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 space-y-1.5"

                             x-data="{

                                openDropdown: false,

                                options: {

                                    5: '5 - Responsif',

                                    4: '4 - Jelas & Baik',

                                    3: '3 - Cukup',

                                    2: '2 - Kurang Jelas',

                                    1: '1 - Sulit Dihubungi'

                                }

                             }">



                            <label class="block text-[11px] font-bold text-gray-700 dark:text-gray-300">



                                Komunikasi <span class="text-rose-500">*</span>



                            </label>



                            <input type="hidden" name="komunikasi" :value="rateKomunikasi">



                            <div class="relative">



                                <button type="button"

                                        @click="openDropdown = !openDropdown"

                                        class="w-full flex items-center justify-between py-1.5 px-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 font-semibold text-gray-800 dark:text-white hover:bg-gray-50 transition cursor-pointer">



                                    <span x-text="options[rateKomunikasi] || 'Pilih Nilai'"></span>



                                    <svg class="w-3.5 h-3.5 text-gray-400 transition-transform duration-200 shrink-0" :class="{ 'rotate-180': openDropdown }" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">



                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>



                                    </svg>



                                </button>



                                <div x-show="openDropdown"

                                     @click.outside="openDropdown = false"

                                     x-transition

                                     class="absolute z-50 top-full mt-1 left-0 w-full bg-white dark:bg-gray-900 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 p-1 space-y-0.5 overflow-hidden"

                                     style="display: none;">



                                    <template x-for="(label, val) in options" :key="val">



                                        <button type="button"

                                                @click="rateKomunikasi = parseInt(val); openDropdown = false"

                                                class="w-full text-left px-2.5 py-1.5 text-xs font-bold rounded text-gray-800 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-700 dark:hover:text-amber-300 transition cursor-pointer"

                                                :class="rateKomunikasi === parseInt(val) ? 'bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 font-black' : ''"

                                                x-text="label">



                                        </button>



                                    </template>



                                </div>



                            </div>



                        </div>







                        <!-- Aspek 3: Kerjasama -->



                        <div class="p-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 space-y-1.5"

                             x-data="{

                                openDropdown: false,

                                options: {

                                    5: '5 - Sangat Ramah',

                                    4: '4 - Kooperatif',

                                    3: '3 - Cukup',

                                    2: '2 - Kurang Kooperatif',

                                    1: '1 - Tidak Bersahabat'

                                }

                             }">



                            <label class="block text-[11px] font-bold text-gray-700 dark:text-gray-300">



                                Kerjasama & Sikap <span class="text-rose-500">*</span>



                            </label>



                            <input type="hidden" name="kerjasama" :value="rateKerjasama">



                            <div class="relative">



                                <button type="button"

                                        @click="openDropdown = !openDropdown"

                                        class="w-full flex items-center justify-between py-1.5 px-2 text-xs rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 font-semibold text-gray-800 dark:text-white hover:bg-gray-50 transition cursor-pointer">



                                    <span x-text="options[rateKerjasama] || 'Pilih Nilai'"></span>



                                    <svg class="w-3.5 h-3.5 text-gray-400 transition-transform duration-200 shrink-0" :class="{ 'rotate-180': openDropdown }" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">



                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>



                                    </svg>



                                </button>



                                <div x-show="openDropdown"

                                     @click.outside="openDropdown = false"

                                     x-transition

                                     class="absolute z-50 top-full mt-1 left-0 w-full bg-white dark:bg-gray-900 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 p-1 space-y-0.5 overflow-hidden"

                                     style="display: none;">



                                    <template x-for="(label, val) in options" :key="val">



                                        <button type="button"

                                                @click="rateKerjasama = parseInt(val); openDropdown = false"

                                                class="w-full text-left px-2.5 py-1.5 text-xs font-bold rounded text-gray-800 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-700 dark:hover:text-amber-300 transition cursor-pointer"

                                                :class="rateKerjasama === parseInt(val) ? 'bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 font-black' : ''"

                                                x-text="label">



                                        </button>



                                    </template>



                                </div>



                            </div>



                        </div>



                    </div>







                    <!-- Catatan Pengalaman Memotret Klien (Rule #73) -->



                    <div>



                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">



                            Catatan Ulasan Pengalaman Sesi (Opsional)



                        </label>



                        <textarea name="ulasan" 



                                  x-model="rateUlasan"



                                  rows="3" 



                                  placeholder="Contoh: Klien hadir tepat waktu di lokasi pantai, sangat kooperatif selama pengarahan pose, dan komunikatif..." 



                                  class="w-full px-3.5 py-2.5 text-xs text-gray-800 dark:text-white bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-amber-400 focus:outline-none transition leading-relaxed"></textarea>



                    </div>







                    <!-- Tombol Aksi di Tengah Maksimal 2 Tombol (Rule #59, #74, #76) -->

                    <p x-show="rateFormError" x-text="rateFormError" x-cloak class="text-[11px] font-bold text-red-500 text-center"></p>

                    <div class="pt-2 flex items-center justify-center gap-3">



                        <button type="button" 



                                @click="closeRateModal()" 



                                class="px-5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 font-bold text-xs hover:bg-gray-100 dark:hover:bg-gray-750 transition cursor-pointer">



                            Batal



                        </button>



                        <button type="submit" 



                                class="px-5 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs shadow-xs transition cursor-pointer">



                            Simpan Data



                        </button>



                    </div>



                </form>







            </div>



        </div>



    </div>







</div>



@endsection