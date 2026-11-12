@extends('layouts.photographer')



@section('title', 'Atur Tanggal Libur — Fotografer')



@section('content')

<div class="space-y-6 w-full" x-data="availabilityFormApp()">



    <!-- Standard Header -->

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

        <div>
            <!-- Breadcrumb Navigation (Rule #62 Uniform Neutral) -->
            <nav class="flex items-center gap-1.5 text-xs text-gray-400 dark:text-gray-500 mb-1.5">
                <a href="{{ route('photographer.availability', ['tab' => 'overrides']) }}" class="hover:text-amber-500 transition">Jadwal</a>
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                <span>Atur Tanggal Libur</span>
            </nav>
            <h1 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">
                Atur Tanggal Libur
            </h1>
        </div>

        <div>

            <a href="{{ route('photographer.availability', ['tab' => 'overrides']) }}" class="px-4 py-2 rounded-xl bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 font-bold text-xs transition inline-flex items-center gap-1.5 shadow-2xs shrink-0">

                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>

                <span>Kembali</span>

            </a>

        </div>

    </div>



    <!-- Main Vertical Form Card -->

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm p-6 sm:p-8">

        

        <form action="{{ route('photographer.availability.store') }}" method="POST" @submit="submitForm($event)" novalidate class="space-y-6">

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

                        <input type="radio" name="tipe_blokir" value="all_day" x-model="tipeBlokir" class="sr-only peer">

                        <span class="mt-0.5 w-4 h-4 rounded-full border-2 flex items-center justify-center shrink-0 transition peer-focus-visible:ring-2 peer-focus-visible:ring-amber-400"
                              :class="tipeBlokir === 'all_day' ? 'border-amber-500 bg-white dark:bg-gray-800' : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900'">
                            <span class="w-2 h-2 rounded-full bg-amber-500" x-show="tipeBlokir === 'all_day'"></span>
                        </span>

                        <div>

                            <span class="text-xs font-black block">Libur Seharian</span>

                            <span class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5 block leading-relaxed">

                                Tutup pemesanan untuk satu hari atau beberapa hari berturut-turut.

                            </span>

                        </div>

                    </label>



                    <!-- Opsi B: Jam Tertentu / Slot Sesi -->

                    <label @click="tipeBlokir = 'time_slot'" 

                           :class="tipeBlokir === 'time_slot' ? 'border-amber-500 bg-amber-50/60 dark:bg-amber-950/40 text-amber-950 dark:text-amber-200 shadow-2xs' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 hover:border-gray-300'"

                           class="p-4 rounded-xl border-2 transition cursor-pointer flex items-start gap-3 select-none">

                        <input type="radio" name="tipe_blokir" value="time_slot" x-model="tipeBlokir" class="sr-only peer">

                        <span class="mt-0.5 w-4 h-4 rounded-full border-2 flex items-center justify-center shrink-0 transition peer-focus-visible:ring-2 peer-focus-visible:ring-amber-400"
                              :class="tipeBlokir === 'time_slot' ? 'border-amber-500 bg-white dark:bg-gray-800' : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900'">
                            <span class="w-2 h-2 rounded-full bg-amber-500" x-show="tipeBlokir === 'time_slot'"></span>
                        </span>

                        <div>

                            <span class="text-xs font-black block">Tutup Jam Tertentu</span>

                            <span class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5 block leading-relaxed">

                                Tutup rentang jam tertentu pada satu tanggal.

                            </span>

                        </div>

                    </label>



                </div>

            </div>



            <!-- 2A. Field jika Seharian Penuh -->

            <div x-show="tipeBlokir === 'all_day'" class="space-y-5">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

                    <div>

                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">

                            Tanggal Mulai Libur <span class="text-rose-500">*</span>

                        </label>

                        <input type="date" 
                               name="tanggal_mulai" 
                               x-ref="fieldTanggalMulai"
                               x-model="form.tanggal_mulai"
                               @input="touched.tanggal_mulai = true; validateAllDay()"
                               @blur="touched.tanggal_mulai = true; validateAllDay()"
                               class="w-full px-4 py-3 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition"
                               :class="touched.tanggal_mulai && errors.tanggal_mulai ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500 focus:border-amber-500'">
                        <p x-show="touched.tanggal_mulai && errors.tanggal_mulai" x-text="errors.tanggal_mulai" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>

                    </div>



                    <div>

                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">

                            Tanggal Selesai Libur <span class="text-rose-500">*</span>

                        </label>

                        <input type="date" 
                               name="tanggal_selesai" 
                               x-ref="fieldTanggalSelesai"
                               x-model="form.tanggal_selesai"
                               @input="touched.tanggal_selesai = true; validateAllDay()"
                               @blur="touched.tanggal_selesai = true; validateAllDay()"
                               class="w-full px-4 py-3 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition"
                               :class="touched.tanggal_selesai && errors.tanggal_selesai ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500 focus:border-amber-500'">

                        <p class="text-[11px] text-gray-400 mt-1">Isi tanggal yang sama jika hanya libur 1 hari.</p>

                        <p x-show="touched.tanggal_selesai && errors.tanggal_selesai" x-text="errors.tanggal_selesai" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>

                    </div>

                </div>

            </div>



            <!-- 2B. Field jika Tutup Jam Tertentu -->

            <div x-show="tipeBlokir === 'time_slot'" x-cloak class="space-y-5">

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">

                    <div>

                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">

                            Tanggal <span class="text-rose-500">*</span>

                        </label>

                        <input type="date" 
                               name="tanggal" 
                               x-ref="fieldTanggal"
                               x-model="form.tanggal"
                               @input="touched.tanggal = true; validateTanggal()"
                               @blur="touched.tanggal = true; validateTanggal()"
                               class="w-full px-4 py-3 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition"
                               :class="touched.tanggal && errors.tanggal ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500 focus:border-amber-500'">

                        <p x-show="touched.tanggal && errors.tanggal" x-text="errors.tanggal" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>

                    </div>



                    <div>

                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">

                            Dari Jam <span class="text-rose-500">*</span>

                        </label>

                        <x-time-dropdown
                            model="form.jam_mulai"
                            name="jam_mulai"
                            trigger-ref="fieldJamMulai"
                            error="touched.jam_mulai && errors.jam_mulai"
                            on-select="touched.jam_mulai = true; validateJamMulai()"
                            trigger-class="w-full px-4 py-3 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs flex items-center justify-between gap-2 outline-none transition cursor-pointer"
                        />

                        <p x-show="touched.jam_mulai && errors.jam_mulai" x-text="errors.jam_mulai" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>

                    </div>



                    <div>

                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">

                            Sampai Jam <span class="text-rose-500">*</span>

                        </label>

                        <x-time-dropdown
                            model="form.jam_selesai"
                            name="jam_selesai"
                            trigger-ref="fieldJamSelesai"
                            error="touched.jam_selesai && errors.jam_selesai"
                            on-select="touched.jam_selesai = true; validateJamSelesai()"
                            align="right"
                            trigger-class="w-full px-4 py-3 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs flex items-center justify-between gap-2 outline-none transition cursor-pointer"
                        />

                        <p x-show="touched.jam_selesai && errors.jam_selesai" x-text="errors.jam_selesai" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>

                    </div>

                </div>

            </div>



            <!-- 3. Keterangan / Alasan -->

            <div>

                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">

                    Keterangan (Opsional)

                </label>

                <textarea name="keterangan" 

                          rows="4" 

                          placeholder="Masukkan keterangan libur jika diperlukan" 

                          class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition resize-y @error('keterangan') border-rose-500 @enderror">{{ old('keterangan') }}</textarea>

                @error('keterangan')

                    <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>

                @enderror

            </div>



            <!-- Action Button Footer (Rule #74 Standard: Batal di samping kiri tombol submit, rata kanan gap-3, Rule #41: No border-t Divider) -->
            <div class="flex items-center justify-end gap-3 pt-4">

                <a href="{{ route('photographer.availability', ['tab' => 'overrides']) }}" class="px-5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold text-xs transition text-center shadow-2xs cursor-pointer">

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
function availabilityFormApp() {
    return {
        tipeBlokir: @js(old('tipe_blokir', 'all_day')),
        form: {
            tanggal_mulai: @js(old('tanggal_mulai', date('Y-m-d'))),
            tanggal_selesai: @js(old('tanggal_selesai', date('Y-m-d'))),
            tanggal: @js(old('tanggal', date('Y-m-d'))),
            jam_mulai: @js(old('jam_mulai', '')),
            jam_selesai: @js(old('jam_selesai', ''))
        },
        touched: {
            tanggal_mulai: {{ $errors->has('tanggal_mulai') ? 'true' : 'false' }},
            tanggal_selesai: {{ $errors->has('tanggal_selesai') ? 'true' : 'false' }},
            tanggal: {{ $errors->has('tanggal') ? 'true' : 'false' }},
            jam_mulai: {{ $errors->has('jam_mulai') ? 'true' : 'false' }},
            jam_selesai: {{ $errors->has('jam_selesai') ? 'true' : 'false' }}
        },
        errors: {
            tanggal_mulai: '{{ $errors->first('tanggal_mulai') ?: '' }}',
            tanggal_selesai: '{{ $errors->first('tanggal_selesai') ?: '' }}',
            tanggal: '{{ $errors->first('tanggal') ?: '' }}',
            jam_mulai: '{{ $errors->first('jam_mulai') ?: '' }}',
            jam_selesai: '{{ $errors->first('jam_selesai') ?: '' }}'
        },
        validateTanggalMulai() {
            if (!this.form.tanggal_mulai) {
                this.errors.tanggal_mulai = 'Tanggal mulai wajib diisi.';
            } else {
                this.errors.tanggal_mulai = '';
            }
            if (this.touched.tanggal_selesai) {
                this.validateTanggalSelesai();
            }
        },
        validateTanggalSelesai() {
            if (!this.form.tanggal_selesai) {
                this.errors.tanggal_selesai = 'Tanggal selesai libur wajib diisi.';
            } else if (this.form.tanggal_mulai && this.form.tanggal_selesai < this.form.tanggal_mulai) {
                this.errors.tanggal_selesai = 'Tanggal selesai tidak boleh sebelum tanggal mulai.';
            } else {
                this.errors.tanggal_selesai = '';
            }
        },
        validateAllDay() {
            this.validateTanggalMulai();
            this.validateTanggalSelesai();
        },
        validateTanggal() {
            if (!this.form.tanggal) {
                this.errors.tanggal = 'Tanggal wajib diisi.';
            } else {
                this.errors.tanggal = '';
            }
        },
        validateJamMulai() {
            if (!this.form.jam_mulai) {
                this.errors.jam_mulai = 'Jam mulai wajib diisi.';
            } else {
                this.errors.jam_mulai = '';
            }
            if (this.touched.jam_selesai) {
                this.validateJamSelesai();
            }
        },
        validateJamSelesai() {
            if (!this.form.jam_selesai) {
                this.errors.jam_selesai = 'Jam selesai wajib diisi.';
            } else if (this.form.jam_mulai && this.form.jam_selesai <= this.form.jam_mulai) {
                this.errors.jam_selesai = 'Jam selesai harus lebih akhir dari jam mulai.';
            } else {
                this.errors.jam_selesai = '';
            }
        },
        submitForm(e) {
            let hasError = false;
            let firstInvalidRef = null;

            if (this.tipeBlokir === 'all_day') {
                this.touched.tanggal_mulai = true;
                this.touched.tanggal_selesai = true;
                this.validateTanggalMulai();
                this.validateTanggalSelesai();

                if (this.errors.tanggal_mulai) {
                    hasError = true;
                    firstInvalidRef = firstInvalidRef || this.$refs.fieldTanggalMulai;
                }
                if (this.errors.tanggal_selesai) {
                    hasError = true;
                    firstInvalidRef = firstInvalidRef || this.$refs.fieldTanggalSelesai;
                }
            } else {
                this.touched.tanggal = true;
                this.touched.jam_mulai = true;
                this.touched.jam_selesai = true;
                this.validateTanggal();
                this.validateJamMulai();
                this.validateJamSelesai();

                if (this.errors.tanggal) {
                    hasError = true;
                    firstInvalidRef = firstInvalidRef || this.$refs.fieldTanggal;
                }
                if (this.errors.jam_mulai) {
                    hasError = true;
                    firstInvalidRef = firstInvalidRef || this.$refs.fieldJamMulai;
                }
                if (this.errors.jam_selesai) {
                    hasError = true;
                    firstInvalidRef = firstInvalidRef || this.$refs.fieldJamSelesai;
                }
            }

            if (hasError) {
                e.preventDefault();
                if (firstInvalidRef) {
                    const focusEl = firstInvalidRef.matches('button,input,select,textarea')
                        ? firstInvalidRef
                        : (firstInvalidRef.querySelector('[data-time-trigger]') || firstInvalidRef);
                    focusEl.focus();
                    firstInvalidRef.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        }
    };
}
</script>
@endsection

