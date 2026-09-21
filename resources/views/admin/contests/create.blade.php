@extends('layouts.admin')

@section('title', 'Buat Lomba Foto Baru — Admin')

@section('content')
<div class="space-y-6">

    <!-- Standard Golden Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <span class="text-[11px] font-extrabold uppercase tracking-widest text-amber-600 dark:text-amber-500 block mb-1">
                Lomba Foto
            </span>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">
                Tambah Lomba Foto
            </h1>
            <p class="text-xs text-gray-500 dark:text-gray-400 font-medium mt-1">
                Buat dan publikasikan event lomba foto baru.
            </p>
        </div>
        <div>
            <a href="{{ route('admin.contests') }}" class="px-4 py-2 rounded-xl bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-750 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 font-bold text-xs transition inline-flex items-center gap-1.5 shadow-2xs shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"></path></svg>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <!-- Main Vertical Form Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm p-6 sm:p-8"
         x-data="{
            previewUrl: null,
            fileChosen(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => { this.previewUrl = e.target.result; };
                    reader.readAsDataURL(file);
                }
            }
         }">
        <form action="{{ route('admin.contests.store') }}" method="POST" enctype="multipart/form-data" class="space-y-7 sm:space-y-8">
            @csrf

            <!-- 1. Judul Lomba -->
            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-2">
                    Judul Lomba <span class="text-rose-500">*</span>
                </label>
                <input type="text" 
                       name="judul_lomba" 
                       value="{{ old('judul_lomba') }}"
                       required 
                       placeholder="Contoh: LensMatch National Photography Contest: Pesona Budaya Nusantara" 
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition @error('judul_lomba') border-rose-500 @enderror">
                @error('judul_lomba')
                    <p class="text-[11px] text-rose-500 font-bold mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- 2. Deskripsi Lomba (Tepat di bawah Judul Lomba) -->
            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-2">
                    Deskripsi Lomba <span class="text-rose-500">*</span>
                </label>
                <textarea name="deskrpisi_lomba" 
                          rows="7" 
                          required 
                          placeholder="Tuliskan latar belakang tema kompetisi dan ruang lingkup karya..." 
                          class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition resize-y leading-relaxed min-h-[160px] @error('deskrpisi_lomba') border-rose-500 @enderror">{{ old('deskrpisi_lomba') }}</textarea>
                @error('deskrpisi_lomba')
                    <p class="text-[11px] text-rose-500 font-bold mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- 3. Kategori & Penyelenggara -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 sm:gap-6">
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-2">
                        Kategori <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           name="kategori" 
                           value="{{ old('kategori', 'Landscape & Alam') }}"
                           required 
                           placeholder="mis. Landscape, Portrait, Street, Budaya" 
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition @error('kategori') border-rose-500 @enderror">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-2">
                        Penyelenggara <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           name="penyelenggara" 
                           value="{{ old('penyelenggara', 'LensMatch Official') }}"
                           required 
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition @error('penyelenggara') border-rose-500 @enderror">
                </div>
            </div>

            <!-- 4. Total Hadiah & Status -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 sm:gap-6">
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-2">
                        Total Hadiah <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           name="hadiah" 
                           value="{{ old('hadiah', 'Rp 15.000.000 + E-Sertifikat') }}"
                           required 
                           placeholder="Contoh: Rp 15.000.000" 
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition @error('hadiah') border-rose-500 @enderror">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-2">
                        Status <span class="text-rose-500">*</span>
                    </label>
                    @php
                        $statusOptions = [
                            'buka' => 'Aktif Buka',
                            'pending_review' => 'Pending Review',
                            'ditutup' => 'Ditutup',
                            'selesai' => 'Selesai',
                            'ditolak' => 'Ditolak'
                        ];
                        $selectedStatus = old('status', 'buka');
                        $selectedStatusLabel = $statusOptions[$selectedStatus] ?? 'Aktif Buka';
                    @endphp
                    <div class="relative w-full" x-data="{ openStatus: false, statusVal: '{{ $selectedStatus }}', statusLabel: '{{ $selectedStatusLabel }}' }">
                        <input type="hidden" name="status" :value="statusVal" required>
                        <button @click="openStatus = !openStatus" type="button" 
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs font-bold text-gray-900 dark:text-white flex items-center justify-between gap-2 shadow-2xs hover:border-amber-400 dark:hover:border-amber-500 cursor-pointer transition">
                            <span x-text="statusLabel" class="truncate"></span>
                            <svg class="w-4 h-4 text-gray-400 shrink-0 transition-transform duration-200" :class="openStatus ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="openStatus" @click.away="openStatus = false" x-transition 
                             class="absolute z-50 left-0 mt-2 w-full bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700/80 py-1 overflow-hidden" style="display: none;">
                            @foreach($statusOptions as $val => $label)
                                <button type="button" 
                                        @click="statusVal = '{{ $val }}'; statusLabel = '{{ $label }}'; openStatus = false" 
                                        class="w-full px-4 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition flex items-center justify-between text-left cursor-pointer border-b border-gray-50 dark:border-gray-700/40 last:border-0"
                                        :class="statusVal === '{{ $val }}' ? 'bg-amber-50/70 dark:bg-amber-950/30 text-amber-600 font-extrabold' : ''">
                                    <span>{{ $label }}</span>
                                    <span x-show="statusVal === '{{ $val }}'" class="text-amber-600 font-bold">✓</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- 5. Biaya Pendaftaran -->
            @php
                $initialFeeAmount = old('biaya_pendaftaran', 0);
                $initialFeeType = $initialFeeAmount > 0 ? 'paid' : 'free';
                $initialFeeLabel = $initialFeeType === 'paid' ? 'Berbayar (Transfer Bank Manual)' : 'Gratis (Free)';
            @endphp
            <div class="space-y-5" x-data="{ 
                    openFee: false, 
                    feeType: '{{ $initialFeeType }}', 
                    feeLabel: '{{ $initialFeeLabel }}' 
                 }">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 sm:gap-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-2">
                            Tipe Biaya Pendaftaran <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative w-full">
                            <button @click="openFee = !openFee" type="button" 
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs font-bold text-gray-900 dark:text-white flex items-center justify-between gap-2 shadow-2xs hover:border-amber-400 dark:hover:border-amber-500 cursor-pointer transition">
                                <span x-text="feeLabel" class="truncate"></span>
                                <svg class="w-4 h-4 text-gray-400 shrink-0 transition-transform duration-200" :class="openFee ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div x-show="openFee" @click.away="openFee = false" x-transition 
                                 class="absolute z-50 left-0 mt-2 w-full bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700/80 py-1 overflow-hidden" style="display: none;">
                                <button type="button" 
                                        @click="feeType = 'free'; feeLabel = 'Gratis (Free)'; openFee = false" 
                                        class="w-full px-4 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition flex items-center justify-between text-left cursor-pointer border-b border-gray-50 dark:border-gray-700/40"
                                        :class="feeType === 'free' ? 'bg-amber-50/70 dark:bg-amber-950/30 text-amber-600 font-extrabold' : ''">
                                    <span>Gratis (Free)</span>
                                    <span x-show="feeType === 'free'" class="text-amber-600 font-bold">✓</span>
                                </button>
                                <button type="button" 
                                        @click="feeType = 'paid'; feeLabel = 'Berbayar (Transfer Bank Manual)'; openFee = false" 
                                        class="w-full px-4 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition flex items-center justify-between text-left cursor-pointer"
                                        :class="feeType === 'paid' ? 'bg-amber-50/70 dark:bg-amber-950/30 text-amber-600 font-extrabold' : ''">
                                    <span>Berbayar (Transfer Bank Manual)</span>
                                    <span x-show="feeType === 'paid'" class="text-amber-600 font-bold">✓</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div x-show="feeType === 'paid'" x-cloak>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-2">
                            Biaya Registrasi (Rp) <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" name="biaya_pendaftaran" value="{{ old('biaya_pendaftaran', 0) }}" min="0" step="5000" placeholder="mis. 50000" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition">
                    </div>
                </div>

                <div x-show="feeType === 'paid'" x-cloak class="grid grid-cols-1 sm:grid-cols-3 gap-5 sm:gap-6 pt-1">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-2">Nama Bank</label>
                        <input type="text" name="bank_name" value="{{ old('bank_name', 'BCA') }}" placeholder="mis. BCA / Mandiri" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-2">Nomor Rekening</label>
                        <input type="text" name="bank_account_number" value="{{ old('bank_account_number') }}" placeholder="mis. 8271928391" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-2">Atas Nama</label>
                        <input type="text" name="bank_account_holder" value="{{ old('bank_account_holder') }}" placeholder="mis. PT LensMatch Indonesia" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition">
                    </div>
                </div>
            </div>

            <!-- 6. Maksimal Karya & Format Media -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 sm:gap-6">
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-2">
                        Maksimal Karya per Peserta <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" name="max_submissions" value="{{ old('max_submissions', 2) }}" min="1" max="10" required class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-2">
                        Tautan Media Eksternal <span class="text-rose-500">*</span>
                    </label>
                    @php
                        $initialAllowLink = old('allow_link', '1');
                        $initialAllowLinkLabel = $initialAllowLink === '1' ? 'Diizinkan (Google Drive / YouTube)' : 'Tidak Diizinkan';
                    @endphp
                    <div class="relative w-full" x-data="{ openLink: false, allowLinkVal: '{{ $initialAllowLink }}', allowLinkLabel: '{{ $initialAllowLinkLabel }}' }">
                        <input type="hidden" name="allow_link" :value="allowLinkVal">
                        <button @click="openLink = !openLink" type="button" 
                                class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs font-bold text-gray-900 dark:text-white flex items-center justify-between gap-2 shadow-2xs hover:border-amber-400 dark:hover:border-amber-500 cursor-pointer transition">
                            <span x-text="allowLinkLabel" class="truncate"></span>
                            <svg class="w-4 h-4 text-gray-400 shrink-0 transition-transform duration-200" :class="openLink ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="openLink" @click.away="openLink = false" x-transition 
                             class="absolute z-50 left-0 mt-2 w-full bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700/80 py-1 overflow-hidden" style="display: none;">
                            <button type="button" 
                                    @click="allowLinkVal = '1'; allowLinkLabel = 'Diizinkan (Google Drive / YouTube)'; openLink = false" 
                                    class="w-full px-4 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition flex items-center justify-between text-left cursor-pointer border-b border-gray-50 dark:border-gray-700/40"
                                    :class="allowLinkVal === '1' ? 'bg-amber-50/70 dark:bg-amber-950/30 text-amber-600 font-extrabold' : ''">
                                <span>Diizinkan (Google Drive / YouTube)</span>
                                <span x-show="allowLinkVal === '1'" class="text-amber-600 font-bold">✓</span>
                            </button>
                            <button type="button" 
                                    @click="allowLinkVal = '0'; allowLinkLabel = 'Tidak Diizinkan'; openLink = false" 
                                    class="w-full px-4 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition flex items-center justify-between text-left cursor-pointer"
                                    :class="allowLinkVal === '0' ? 'bg-amber-50/70 dark:bg-amber-950/30 text-amber-600 font-extrabold' : ''">
                                <span>Tidak Diizinkan</span>
                                <span x-show="allowLinkVal === '0'" class="text-amber-600 font-bold">✓</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 7. Linimasa Lomba -->
            <div class="p-6 sm:p-7 rounded-2xl bg-gray-50/70 dark:bg-gray-900/50 border border-gray-200/80 dark:border-gray-700/80 space-y-6">
                <div>
                    <h4 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-wider">Linimasa Lomba</h4>
                </div>

                <!-- Fase 1: Registrasi Peserta (2 Kolom) -->
                <div class="space-y-2.5">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-gray-700 dark:text-gray-300 block">1. Tahap Registrasi</span>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 dark:text-gray-300 mb-1.5">Mulai Registrasi</label>
                            <input type="date" name="registration_start" value="{{ old('registration_start', date('Y-m-d')) }}" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 dark:text-gray-300 mb-1.5">Tutup Registrasi</label>
                            <input type="date" name="registration_end" value="{{ old('registration_end', date('Y-m-d', strtotime('+10 days'))) }}" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                    </div>
                </div>

                <!-- Fase 2: Pengumpulan Karya Foto (2 Kolom) -->
                <div class="space-y-2.5 pt-4 border-t border-gray-200/60 dark:border-gray-700/60">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-gray-700 dark:text-gray-300 block">2. Tahap Pengumpulan Karya</span>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 dark:text-gray-300 mb-1.5">Mulai Upload Karya</label>
                            <input type="date" name="submission_start" value="{{ old('submission_start', date('Y-m-d', strtotime('+11 days'))) }}" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 dark:text-gray-300 mb-1.5">Batas Upload Karya</label>
                            <input type="date" name="submission_end" value="{{ old('submission_end', date('Y-m-d', strtotime('+22 days'))) }}" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                    </div>
                </div>

                <!-- Fase 3: Penjurian & Pengumuman (3 Kolom) -->
                <div class="space-y-2.5 pt-4 border-t border-gray-200/60 dark:border-gray-700/60">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-gray-700 dark:text-gray-300 block">3. Tahap Penjurian & Pengumuman</span>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-5">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 dark:text-gray-300 mb-1.5">Mulai Penjurian</label>
                            <input type="date" name="judging_start" value="{{ old('judging_start', date('Y-m-d', strtotime('+23 days'))) }}" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 dark:text-gray-300 mb-1.5">Selesai Penjurian</label>
                            <input type="date" name="judging_end" value="{{ old('judging_end', date('Y-m-d', strtotime('+26 days'))) }}" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 dark:text-gray-300 mb-1.5">Pengumuman Pemenang</label>
                            <input type="date" name="announcement_date" value="{{ old('announcement_date', date('Y-m-d', strtotime('+27 days'))) }}" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                    </div>
                </div>

                <!-- Rentang Keseluruhan Event (2 Kolom) -->
                <div class="space-y-2.5 pt-4 border-t border-gray-200/60 dark:border-gray-700/60">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-gray-700 dark:text-gray-300 block">4. Periode Keseluruhan Event</span>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 dark:text-gray-300 mb-1.5">Mulai Event <span class="text-rose-500">*</span></label>
                            <input type="date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 dark:text-gray-300 mb-1.5">Selesai Event <span class="text-rose-500">*</span></label>
                            <input type="date" name="end_date" value="{{ old('end_date', date('Y-m-d', strtotime('+30 days'))) }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                    </div>
                </div>
            </div>

            <!-- 8. Syarat & Ketentuan -->
            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-2">
                    Syarat & Ketentuan
                </label>
                <textarea name="syarat_ketentuan" 
                          rows="10" 
                          placeholder="Tuliskan poin-poin syarat partisipasi, orisinalitas, teknis file foto, hak cipta, dan ketentuan juri..." 
                          class="w-full px-4 py-3.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition resize-y leading-relaxed font-mono min-h-[220px]">{{ old('syarat_ketentuan') }}</textarea>
            </div>

            <!-- 9. Poster / Banner Lomba -->
            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-2">
                    Poster / Banner Lomba
                </label>
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 sm:gap-5">
                    <div class="w-48 h-28 rounded-2xl bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 flex items-center justify-center overflow-hidden shrink-0 shadow-xs">
                        <template x-if="previewUrl">
                            <img :src="previewUrl" onerror="this.onerror=null;this.src=window.SVG_IMAGE_FALLBACK;" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!previewUrl">
                            <div class="text-center p-3 text-gray-400">
                                <svg class="w-8 h-8 mx-auto mb-1 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
                                <span class="text-[10px] font-bold">Poster Lomba</span>
                            </div>
                        </template>
                    </div>

                    <div class="space-y-2.5 flex-1">
                        <label class="cursor-pointer inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs shadow-xs transition">
                            <span>Upload Poster</span>
                            <input type="file" name="banner_file" accept="image/png, image/jpeg, image/webp" @change="fileChosen" class="hidden">
                        </label>
                        <input type="url" name="banner_url" placeholder="Atau masukkan URL banner..." value="{{ old('banner_url') }}" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none">
                    </div>
                </div>
            </div>

            <!-- Action Button Footer -->
            <div class="flex items-center justify-end pt-4">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs shadow-xs transition">
                    Tambah Data
                </button>
            </div>
        </form>
    </div>

</div>
@endsection