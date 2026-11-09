@extends('layouts.photographer')



@section('title', 'Blokir Jadwal & Libur — Fotografer')



@section('content')

<div class="space-y-6 w-full" x-data="{ tipeBlokir: '{{ old('tipe_blokir', 'all_day') }}' }">



    <!-- Standard Header -->

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

        <div>
            <!-- Breadcrumb Navigation -->
            <nav class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 mb-1.5">
                <a href="{{ route('photographer.availability') }}" class="hover:text-amber-500 transition">Kalender Jadwal</a>
                <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                <span class="text-gray-900 dark:text-white font-bold">Blokir Jadwal</span>
            </nav>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">
                Tutup Jadwal / Libur Pemotretan
            </h1>
        </div>

        <div>

            <a href="{{ route('photographer.availability') }}" class="px-4 py-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200/80 dark:border-amber-900/60 font-extrabold text-xs transition inline-flex items-center justify-center shrink-0">

                Kembali ke Kalender

            </a>

        </div>

    </div>



    <!-- Main Vertical Form Card -->

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm p-6 sm:p-8">

        

        <form action="{{ route('photographer.availability.store') }}" method="POST" class="space-y-6">

            @csrf



            <!-- 1. Pilihan Tipe Blokir: Seharian vs Jam Tertentu -->

            <div class="space-y-2">

                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">

                    Tipe Penutupan Jadwal <span class="text-rose-500">*</span>

                </label>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                    

                    <!-- Opsi A: Seharian Penuh -->

                    <label @click="tipeBlokir = 'all_day'" 

                           :class="tipeBlokir === 'all_day' ? 'border-amber-500 bg-amber-50/60 dark:bg-amber-950/40 text-amber-950 dark:text-amber-200 shadow-2xs' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 hover:border-gray-300'"

                           class="p-4 rounded-xl border-2 transition cursor-pointer flex items-start gap-3 select-none">

                        <input type="radio" name="tipe_blokir" value="all_day" x-model="tipeBlokir" class="mt-1 text-amber-500 focus:ring-amber-400">

                        <div>

                            <span class="text-xs font-black block">Libur Seharian Penuh (All Day)</span>

                            <span class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5 block leading-relaxed">

                                Menutup 1 hari atau beberapa hari berturut-turut (misal: acara keluarga, trip luar kota).

                            </span>

                        </div>

                    </label>



                    <!-- Opsi B: Jam Tertentu / Slot Sesi -->

                    <label @click="tipeBlokir = 'time_slot'" 

                           :class="tipeBlokir === 'time_slot' ? 'border-amber-500 bg-amber-50/60 dark:bg-amber-950/40 text-amber-950 dark:text-amber-200 shadow-2xs' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 hover:border-gray-300'"

                           class="p-4 rounded-xl border-2 transition cursor-pointer flex items-start gap-3 select-none">

                        <input type="radio" name="tipe_blokir" value="time_slot" x-model="tipeBlokir" class="mt-1 text-amber-500 focus:ring-amber-400">

                        <div>

                            <span class="text-xs font-black block">Tutup Jam Tertentu (Mendadak / Slot Jam)</span>

                            <span class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5 block leading-relaxed">

                                Hanya menutup rentang jam tertentu pada satu tanggal (misal: tutup sesi sore 15:00 - 19:00).

                            </span>

                        </div>

                    </label>



                </div>

            </div>



            <!-- 2A. Field jika Seharian Penuh -->

            <div x-show="tipeBlokir === 'all_day'" class="space-y-5 pt-2 border-t border-gray-100 dark:border-gray-700/60">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                    <div>

                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">

                            Tanggal Mulai Libur <span class="text-rose-500">*</span>

                        </label>

                        <input type="date" 

                               name="tanggal_mulai" 

                               value="{{ old('tanggal_mulai', date('Y-m-d')) }}"

                               class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition @error('tanggal_mulai') border-rose-500 @enderror">

                        @error('tanggal_mulai')

                            <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>

                        @enderror

                    </div>



                    <div>

                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">

                            Tanggal Selesai Libur <span class="text-rose-500">*</span>

                        </label>

                        <input type="date" 

                               name="tanggal_selesai" 

                               value="{{ old('tanggal_selesai', date('Y-m-d')) }}"

                               class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition @error('tanggal_selesai') border-rose-500 @enderror">

                        <p class="text-[11px] text-gray-400 mt-1">Isi tanggal yang sama jika hanya libur 1 hari.</p>

                        @error('tanggal_selesai')

                            <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>

                        @enderror

                    </div>

                </div>

            </div>



            <!-- 2B. Field jika Tutup Jam Tertentu -->

            <div x-show="tipeBlokir === 'time_slot'" x-cloak class="space-y-5 pt-2 border-t border-gray-100 dark:border-gray-700/60">

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">

                    <div>

                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">

                            Tanggal <span class="text-rose-500">*</span>

                        </label>

                        <input type="date" 

                               name="tanggal" 

                               value="{{ old('tanggal', date('Y-m-d')) }}"

                               class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition @error('tanggal') border-rose-500 @enderror">

                        @error('tanggal')

                            <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>

                        @enderror

                    </div>



                    <div>

                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">

                            Dari Jam <span class="text-rose-500">*</span>

                        </label>

                        <input type="time" 

                               name="jam_mulai" 

                               value="{{ old('jam_mulai') }}"

                               class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition @error('jam_mulai') border-rose-500 @enderror">

                        @error('jam_mulai')

                            <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>

                        @enderror

                    </div>



                    <div>

                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">

                            Sampai Jam <span class="text-rose-500">*</span>

                        </label>

                        <input type="time" 

                               name="jam_selesai" 

                               value="{{ old('jam_selesai') }}"

                               class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition @error('jam_selesai') border-rose-500 @enderror">

                        @error('jam_selesai')

                            <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>

                        @enderror

                    </div>

                </div>

            </div>



            <!-- 3. Keterangan / Alasan -->

            <div>

                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">

                    Alasan / Keterangan Penutupan (Opsional)

                </label>

                <textarea name="keterangan" 

                          rows="3" 

                          placeholder="Contoh: Sudah capai 2 booking harian, istirahat sesi sore, pemeliharaan kamera, atau ada acara mendadak..." 

                          class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition resize-none @error('keterangan') border-rose-500 @enderror">{{ old('keterangan') }}</textarea>

                @error('keterangan')

                    <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>

                @enderror

            </div>



            <!-- Action Button Footer (Rule #74 Standard: Batal di samping kiri tombol kuning, rata kanan gap-3) -->

            <!-- Action Button Footer (Rule #74 Standard: Batal di samping kiri tombol submit, rata kanan gap-3, Rule #41: No border-t Divider) -->
            <div class="flex items-center justify-end gap-3 pt-4">

                <a href="{{ route('photographer.availability') }}" class="px-5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold text-xs transition text-center shadow-2xs cursor-pointer">

                    Batal

                </a>

                <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs shadow-xs transition cursor-pointer">

                    Simpan Data

                </button>

            </div>



        </form>



    </div>



</div>

@endsection

