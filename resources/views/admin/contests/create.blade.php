@extends('layouts.admin')



@section('title', 'Buat Lomba Foto Baru — Admin')



@section('content')

<div class="space-y-6">



    <!-- Standard Golden Header -->

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

        <div>
            <!-- Breadcrumb Navigation -->
            <nav class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 mb-1.5">
                <a href="{{ route('admin.contests') }}" class="hover:text-amber-500 transition">Informasi Lomba</a>
                <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                <span class="text-gray-900 dark:text-white font-bold">Tambah Lomba</span>
            </nav>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">
                Tambah Lomba Foto
            </h1>
        </div>

        <div>

            <a href="{{ route('admin.contests') }}" class="px-4 py-2 rounded-xl bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-750 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 font-bold text-xs transition inline-flex items-center gap-1.5 shadow-2xs shrink-0">

                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"></path></svg>

                <span>Kembali</span>

            </a>

        </div>

    </div>



    <!-- Main Vertical Form Card -->

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm p-6 sm:p-8"
         x-data="adminContestFormApp()">

        <form action="{{ route('admin.contests.store') }}" method="POST" enctype="multipart/form-data" @submit="submitForm($event)" novalidate class="space-y-7 sm:space-y-8">

            @csrf



            <!-- 1. Judul Lomba -->

            <div>

                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-2">

                    Judul Lomba <span class="text-rose-500">*</span>

                </label>

                <input type="text" 
                       name="judul_lomba" 
                       x-ref="fieldJudul"
                       x-model="form.judul_lomba"
                       @input="touched.judul_lomba = true; validateJudul()"
                       @blur="touched.judul_lomba = true; validateJudul()"
                       placeholder="Contoh: LensMatch National Photography Contest: Pesona Budaya Nusantara" 
                       class="w-full px-4 py-3 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition"
                       :class="touched.judul_lomba && errors.judul_lomba ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500 focus:border-amber-500'">
                <p x-show="touched.judul_lomba && errors.judul_lomba" x-text="errors.judul_lomba" x-cloak class="text-[11px] font-bold text-red-500 mt-1.5"></p>

            </div>



            <!-- 2. Deskripsi Lomba (Tepat di bawah Judul Lomba) -->

            <div>

                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-2">

                    Deskripsi Lomba <span class="text-rose-500">*</span>

                </label>

                <textarea name="deskrpisi_lomba" 
                          x-ref="fieldDeskripsi"
                          x-model="form.deskrpisi_lomba"
                          @input="touched.deskrpisi_lomba = true; validateDeskripsi()"
                          @blur="touched.deskrpisi_lomba = true; validateDeskripsi()"
                          rows="7" 
                          placeholder="Tuliskan latar belakang tema kompetisi dan ruang lingkup karya..." 
                          class="w-full px-4 py-3 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition resize-y leading-relaxed min-h-[160px]"
                          :class="touched.deskrpisi_lomba && errors.deskrpisi_lomba ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500 focus:border-amber-500'">{{ old('deskrpisi_lomba') }}</textarea>
                <p x-show="touched.deskrpisi_lomba && errors.deskrpisi_lomba" x-text="errors.deskrpisi_lomba" x-cloak class="text-[11px] font-bold text-red-500 mt-1.5"></p>

            </div>



            <!-- 3. Kategori & Penyelenggara -->

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 sm:gap-6">

                <div>

                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-2">

                        Kategori <span class="text-rose-500">*</span>

                    </label>

                    <input type="text" 
                           name="kategori" 
                           x-ref="fieldKategori"
                           x-model="form.kategori"
                           @input="touched.kategori = true; validateKategori()"
                           @blur="touched.kategori = true; validateKategori()"
                           placeholder="mis. Landscape, Portrait, Street, Budaya" 
                           class="w-full px-4 py-3 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition"
                           :class="touched.kategori && errors.kategori ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500 focus:border-amber-500'">
                    <p x-show="touched.kategori && errors.kategori" x-text="errors.kategori" x-cloak class="text-[11px] font-bold text-red-500 mt-1.5"></p>

                </div>



                <div>

                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-2">

                        Penyelenggara <span class="text-rose-500">*</span>

                    </label>

                    <input type="text" 
                           name="penyelenggara" 
                           x-ref="fieldPenyelenggara"
                           x-model="form.penyelenggara"
                           @input="touched.penyelenggara = true; validatePenyelenggara()"
                           @blur="touched.penyelenggara = true; validatePenyelenggara()"
                           placeholder="Masukkan nama pihak penyelenggara lomba"
                           class="w-full px-4 py-3 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition"
                           :class="touched.penyelenggara && errors.penyelenggara ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500 focus:border-amber-500'">
                    <p x-show="touched.penyelenggara && errors.penyelenggara" x-text="errors.penyelenggara" x-cloak class="text-[11px] font-bold text-red-500 mt-1.5"></p>

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
                           x-ref="fieldHadiah"
                           x-model="form.hadiah"
                           @input="touched.hadiah = true; validateHadiah()"
                           @blur="touched.hadiah = true; validateHadiah()"
                           placeholder="mis. Rp 15.000.000 + E-Sertifikat" 
                           class="w-full px-4 py-3 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition"
                           :class="touched.hadiah && errors.hadiah ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500 focus:border-amber-500'">
                    <p x-show="touched.hadiah && errors.hadiah" x-text="errors.hadiah" x-cloak class="text-[11px] font-bold text-red-500 mt-1.5"></p>

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

                                    <span x-show="statusVal === '{{ $val }}'" class="shrink-0"><svg class="w-4 h-4 text-amber-600 dark:text-amber-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>

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

                                    <span x-show="feeType === 'free'" class="shrink-0"><svg class="w-4 h-4 text-amber-600 dark:text-amber-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>

                                </button>

                                <button type="button" 

                                        @click="feeType = 'paid'; feeLabel = 'Berbayar (Transfer Bank Manual)'; openFee = false" 

                                        class="w-full px-4 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition flex items-center justify-between text-left cursor-pointer"

                                        :class="feeType === 'paid' ? 'bg-amber-50/70 dark:bg-amber-950/30 text-amber-600 font-extrabold' : ''">

                                    <span>Berbayar (Transfer Bank Manual)</span>

                                    <span x-show="feeType === 'paid'" class="shrink-0"><svg class="w-4 h-4 text-amber-600 dark:text-amber-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>

                                </button>

                            </div>

                        </div>

                    </div>



                    <div x-show="feeType === 'paid'" x-cloak>

                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-2">

                            Biaya Registrasi (Rp) <span class="text-rose-500">*</span>

                        </label>

                        <input type="number" name="biaya_pendaftaran" x-model="form.biaya_pendaftaran" @input="touched.biaya_pendaftaran = true; validateBiaya()" @blur="touched.biaya_pendaftaran = true; validateBiaya()" min="0" step="5000" placeholder="mis. 50000" :class="touched.biaya_pendaftaran && errors.biaya_pendaftaran ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500 focus:border-amber-500'" class="w-full px-4 py-3 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 outline-none transition">
                        <p x-show="touched.biaya_pendaftaran && errors.biaya_pendaftaran" x-text="errors.biaya_pendaftaran" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>

                    </div>

                </div>



                <div x-show="feeType === 'paid'" x-cloak class="grid grid-cols-1 sm:grid-cols-3 gap-5 sm:gap-6 pt-1">

                    <div>

                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-2">Nama Bank</label>

                        <input type="text" name="bank_name" value="{{ old('bank_name') }}" placeholder="mis. BCA / Mandiri" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition">

                    </div>

                    <div>

                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-2">Nomor Rekening</label>

                        <input type="text" name="bank_account_number" value="{{ old('bank_account_number') }}" placeholder="Masukkan nomor rekening" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition">

                    </div>

                    <div>

                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-2">Atas Nama</label>

                        <input type="text" name="bank_account_holder" value="{{ old('bank_account_holder') }}" placeholder="Masukkan nama pemilik rekening" class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition">

                    </div>

                </div>

            </div>



            <!-- 6. Maksimal Karya & Format Media -->

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 sm:gap-6">

                <div>

                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-2">

                        Maksimal Karya per Peserta <span class="text-rose-500">*</span>

                    </label>

                    <input type="number" name="max_submissions" x-model="form.max_submissions" @input="touched.max_submissions = true; validateMaxSubmissions()" @blur="touched.max_submissions = true; validateMaxSubmissions()" min="1" max="10" :class="touched.max_submissions && errors.max_submissions ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500 focus:border-amber-500'" class="w-full px-4 py-3 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 outline-none transition">
                    <p x-show="touched.max_submissions && errors.max_submissions" x-text="errors.max_submissions" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>

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

                                <span x-show="allowLinkVal === '1'" class="shrink-0"><svg class="w-4 h-4 text-amber-600 dark:text-amber-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>

                            </button>

                            <button type="button" 

                                    @click="allowLinkVal = '0'; allowLinkLabel = 'Tidak Diizinkan'; openLink = false" 

                                    class="w-full px-4 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition flex items-center justify-between text-left cursor-pointer"

                                    :class="allowLinkVal === '0' ? 'bg-amber-50/70 dark:bg-amber-950/30 text-amber-600 font-extrabold' : ''">

                                <span>Tidak Diizinkan</span>

                                <span x-show="allowLinkVal === '0'" class="shrink-0"><svg class="w-4 h-4 text-amber-600 dark:text-amber-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>

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

                            <input type="date" 
                                   name="start_date" 
                                   x-ref="fieldStartDate"
                                   x-model="form.start_date" 
                                   @input="touched.start_date = true; validateDates()"
                                   @blur="touched.start_date = true; validateDates()"
                                   class="w-full px-3.5 py-2.5 rounded-xl border bg-white dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition"
                                   :class="touched.start_date && errors.start_date ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500 focus:border-amber-500'">
                            <p x-show="touched.start_date && errors.start_date" x-text="errors.start_date" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>

                        </div>

                        <div>

                            <label class="block text-[11px] font-bold text-gray-700 dark:text-gray-300 mb-1.5">Selesai Event <span class="text-rose-500">*</span></label>

                            <input type="date" 
                                   name="end_date" 
                                   x-ref="fieldEndDate"
                                   x-model="form.end_date" 
                                   @input="touched.end_date = true; validateDates()"
                                   @blur="touched.end_date = true; validateDates()"
                                   class="w-full px-3.5 py-2.5 rounded-xl border bg-white dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition"
                                   :class="touched.end_date && errors.end_date ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500 focus:border-amber-500'">
                            <p x-show="touched.end_date && errors.end_date" x-text="errors.end_date" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>

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
                            <div class="flex items-center justify-center p-3 text-gray-400 dark:text-gray-500">
                                <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                                </svg>
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



            <!-- Action Button Footer (Rule #74 Standard: Batal di samping kiri tombol submit, rata kanan gap-3, Rule #41: No border-t Divider) -->
            <div class="flex items-center justify-end gap-3 pt-4">
                <a href="{{ route('admin.contests') }}" class="px-5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold text-xs transition text-center shadow-2xs cursor-pointer">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs shadow-xs transition cursor-pointer">
                    Simpan Data
                </button>
            </div>

        </form>

    </div>

</div>

<script>
function adminContestFormApp() {
    return {
        previewUrl: null,
        fileChosen(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => { this.previewUrl = e.target.result; };
                reader.readAsDataURL(file);
            }
        },
        form: {
            judul_lomba: @js(old('judul_lomba', '')),
            deskrpisi_lomba: @js(old('deskrpisi_lomba', '')),
            kategori: @js(old('kategori', '')),
            penyelenggara: @js(old('penyelenggara', '')),
            hadiah: @js(old('hadiah', '')),
            biaya_pendaftaran: @js(old('biaya_pendaftaran', 0)),
            max_submissions: @js(old('max_submissions', 2)),
            start_date: @js(old('start_date', date('Y-m-d'))),
            end_date: @js(old('end_date', date('Y-m-d', strtotime('+30 days'))))
        },
        touched: {
            judul_lomba: {{ $errors->has('judul_lomba') ? 'true' : 'false' }},
            deskrpisi_lomba: {{ $errors->has('deskrpisi_lomba') ? 'true' : 'false' }},
            kategori: {{ $errors->has('kategori') ? 'true' : 'false' }},
            penyelenggara: {{ $errors->has('penyelenggara') ? 'true' : 'false' }},
            hadiah: {{ $errors->has('hadiah') ? 'true' : 'false' }},
            biaya_pendaftaran: {{ $errors->has('biaya_pendaftaran') ? 'true' : 'false' }},
            max_submissions: {{ $errors->has('max_submissions') ? 'true' : 'false' }},
            start_date: {{ $errors->has('start_date') ? 'true' : 'false' }},
            end_date: {{ $errors->has('end_date') ? 'true' : 'false' }}
        },
        errors: {
            judul_lomba: '{{ $errors->first('judul_lomba') ?: '' }}',
            deskrpisi_lomba: '{{ $errors->first('deskrpisi_lomba') ?: '' }}',
            kategori: '{{ $errors->first('kategori') ?: '' }}',
            penyelenggara: '{{ $errors->first('penyelenggara') ?: '' }}',
            hadiah: '{{ $errors->first('hadiah') ?: '' }}',
            biaya_pendaftaran: '{{ $errors->first('biaya_pendaftaran') ?: '' }}',
            max_submissions: '{{ $errors->first('max_submissions') ?: '' }}',
            start_date: '{{ $errors->first('start_date') ?: '' }}',
            end_date: '{{ $errors->first('end_date') ?: '' }}'
        },
        validateBiaya() {
            if (this.feeType === 'paid') {
                const val = Number(this.form.biaya_pendaftaran);
                if (isNaN(val) || val <= 0) {
                    this.errors.biaya_pendaftaran = 'Biaya registrasi berbayar harus lebih besar dari Rp 0.';
                } else {
                    this.errors.biaya_pendaftaran = '';
                }
            } else {
                this.errors.biaya_pendaftaran = '';
            }
        },
        validateMaxSubmissions() {
            const val = Number(this.form.max_submissions);
            if (!this.form.max_submissions || isNaN(val) || val < 1 || val > 10) {
                this.errors.max_submissions = 'Maksimal karya harus antara 1 sampai 10 karya.';
            } else {
                this.errors.max_submissions = '';
            }
        },
        validateJudul() {
            if (!this.form.judul_lomba || this.form.judul_lomba.trim() === '') {
                this.errors.judul_lomba = 'Judul lomba wajib diisi.';
            } else if (this.form.judul_lomba.trim().length < 5) {
                this.errors.judul_lomba = 'Judul lomba minimal 5 karakter.';
            } else {
                this.errors.judul_lomba = '';
            }
        },
        validateDeskripsi() {
            if (!this.form.deskrpisi_lomba || this.form.deskrpisi_lomba.trim() === '') {
                this.errors.deskrpisi_lomba = 'Deskripsi lomba wajib diisi.';
            } else if (this.form.deskrpisi_lomba.trim().length < 20) {
                this.errors.deskrpisi_lomba = 'Deskripsi lomba minimal 20 karakter.';
            } else {
                this.errors.deskrpisi_lomba = '';
            }
        },
        validateKategori() {
            if (!this.form.kategori || this.form.kategori.trim() === '') {
                this.errors.kategori = 'Kategori lomba wajib diisi.';
            } else {
                this.errors.kategori = '';
            }
        },
        validatePenyelenggara() {
            if (!this.form.penyelenggara || this.form.penyelenggara.trim() === '') {
                this.errors.penyelenggara = 'Nama pihak penyelenggara wajib diisi.';
            } else if (this.form.penyelenggara.trim().length < 3) {
                this.errors.penyelenggara = 'Nama penyelenggara minimal 3 karakter.';
            } else {
                this.errors.penyelenggara = '';
            }
        },
        validateHadiah() {
            if (!this.form.hadiah || this.form.hadiah.trim() === '') {
                this.errors.hadiah = 'Total hadiah lomba wajib diisi.';
            } else {
                this.errors.hadiah = '';
            }
        },
        validateDates() {
            if (!this.form.start_date) {
                this.errors.start_date = 'Tanggal mulai wajib diisi.';
            } else {
                this.errors.start_date = '';
            }
            if (!this.form.end_date) {
                this.errors.end_date = 'Tanggal selesai wajib diisi.';
            } else if (this.form.start_date && this.form.end_date < this.form.start_date) {
                this.errors.end_date = 'Tanggal selesai tidak boleh sebelum tanggal mulai.';
            } else {
                this.errors.end_date = '';
            }
        },
        submitForm(e) {
            this.touched.judul_lomba = true;
            this.touched.deskrpisi_lomba = true;
            this.touched.kategori = true;
            this.touched.penyelenggara = true;
            this.touched.hadiah = true;
            this.touched.start_date = true;
            this.touched.end_date = true;

            this.validateJudul();
            this.validateDeskripsi();
            this.validateKategori();
            this.validatePenyelenggara();
            this.validateHadiah();
            this.validateDates();
            this.validateBiaya();
            this.validateMaxSubmissions();

            let firstInvalidRef = null;
            if (this.errors.judul_lomba) firstInvalidRef = firstInvalidRef || this.$refs.fieldJudul;
            if (this.errors.deskrpisi_lomba) firstInvalidRef = firstInvalidRef || this.$refs.fieldDeskripsi;
            if (this.errors.kategori) firstInvalidRef = firstInvalidRef || this.$refs.fieldKategori;
            if (this.errors.penyelenggara) firstInvalidRef = firstInvalidRef || this.$refs.fieldPenyelenggara;
            if (this.errors.hadiah) firstInvalidRef = firstInvalidRef || this.$refs.fieldHadiah;
            if (this.errors.start_date) firstInvalidRef = firstInvalidRef || this.$refs.fieldStartDate;
            if (this.errors.end_date) firstInvalidRef = firstInvalidRef || this.$refs.fieldEndDate;

            if (this.errors.judul_lomba || this.errors.deskrpisi_lomba || this.errors.kategori || this.errors.penyelenggara || this.errors.hadiah || this.errors.start_date || this.errors.end_date) {
                e.preventDefault();
                if (firstInvalidRef) {
                    firstInvalidRef.focus();
                    firstInvalidRef.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        }
    };
}
</script>
@endsection