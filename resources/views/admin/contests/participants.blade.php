@extends('layouts.admin')



@section('title', 'Daftar Calon Peserta Lomba Foto - LensMatch Admin')



@section('content')

<div class="space-y-6" x-data="{ rejectModal: false, rejectUrl: '', rejectName: '', transferProofModal: false, transferProofUrl: '' }">



    <!-- Header & Back Navigation -->

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

        <div>

            <a href="{{ route('admin.contests') }}" class="text-xs font-bold text-amber-600 hover:underline flex items-center gap-1.5 mb-1">

                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>

                <span>Kembali ke Daftar Event Lomba</span>

            </a>

            <h1 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">

                Calon Peserta: {{ $contest->judul_lomba }}

            </h1>

            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">

                Review data diri, kualifikasi portofolio/CV, dan verifikasi pembayaran pendaftaran peserta.

            </p>

        </div>



        <div class="flex items-center gap-3 shrink-0">

            <span class="px-3.5 py-2 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 font-extrabold text-xs border border-amber-200/60">

                Biaya: {{ $contest->biaya_pendaftaran > 0 ? 'Rp ' . number_format($contest->biaya_pendaftaran, 0, ',', '.') : 'Gratis (Free)' }}

            </span>

            <a href="{{ route('admin.contests.submissions', $contest->id) }}" class="px-4 py-2 rounded-xl bg-gray-900 hover:bg-black text-white dark:bg-gray-100 dark:text-gray-900 font-extrabold text-xs shadow-xs transition flex items-center gap-2">

                <span>Lihat Karya Foto ({{ $contest->submissions_count }})</span>

                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>

            </a>

        </div>

    </div>



    <!-- Participants Table Card -->

    <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-200/80 dark:border-gray-700/80 overflow-hidden shadow-xs">

        <div class="p-5 sm:p-6 border-b border-gray-100 dark:border-gray-700/60 flex items-center justify-between">

            <h2 class="text-sm sm:text-base font-black text-gray-900 dark:text-white">

                Daftar Pendaftar ({{ $participants->count() }} Calon Peserta)

            </h2>

        </div>



        <div class="overflow-x-auto">

            <table class="w-full text-left text-xs">

                <thead class="bg-gray-50/70 dark:bg-gray-900/60 text-[10px] font-black uppercase text-gray-400 dark:text-gray-400 tracking-wider border-b border-gray-100 dark:border-gray-700/60">

                    <tr>

                        <th class="px-6 py-4">Calon Peserta</th>

                        <th class="px-6 py-4">Kontak & Asal</th>

                        <th class="px-6 py-4">Kualifikasi / Portofolio</th>

                        <th class="px-6 py-4">Status Pendaftaran</th>

                        <th class="px-6 py-4">Pembayaran</th>

                        <th class="px-6 py-4">Karya Dikirim</th>

                        <th class="px-6 py-4 text-right">Aksi Admin</th>

                    </tr>

                </thead>

                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60 text-gray-700 dark:text-gray-300">

                    @forelse($participants as $p)

                        <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-750/50 transition">

                            <!-- Peserta -->

                            <td class="px-6 py-4">

                                <div class="font-extrabold text-gray-900 dark:text-white">{{ $p->nama_lengkap }}</div>

                                <div class="text-[11px] text-gray-400 mt-0.5">{{ $p->instansi ?: 'Independen' }}</div>

                                @if($p->bio_singkat)

                                    <p class="text-[10px] text-gray-500 mt-1 max-w-xs line-clamp-1 italic">"{{ $p->bio_singkat }}"</p>

                                @endif

                            </td>



                            <!-- Kontak -->

                            <td class="px-6 py-4 space-y-0.5">

                                <div class="font-semibold">{{ $p->email }}</div>

                                <div class="text-[11px] text-gray-500 dark:text-gray-400 font-medium">WA: {{ $p->no_wa }}</div>

                                <div class="text-[10px] text-gray-400">{{ $p->asal_kota ?: '-' }}</div>

                            </td>



                            <!-- Kualifikasi / Portfolio -->

                            <td class="px-6 py-4 space-y-1">

                                @if($p->cv_portfolio_file)

                                    <a href="{{ $p->cv_portfolio_file }}" target="_blank" class="text-xs font-semibold text-gray-900 dark:text-white underline block">

                                        Lihat CV / Portofolio (PDF)

                                    </a>

                                @endif

                                @if($p->portfolio_link)

                                    <a href="{{ $p->portfolio_link }}" target="_blank" class="text-xs font-semibold text-gray-900 dark:text-white underline block">

                                        Tautan Portofolio / Sosmed

                                    </a>

                                @endif

                                @if(!$p->cv_portfolio_file && !$p->portfolio_link)

                                    <span class="text-[11px] text-gray-400 italic">Tidak dilampirkan</span>

                                @endif

                            </td>



                            <!-- Status Pendaftaran (Pure Colored Text - Rule #14) -->

                            <td class="px-6 py-4">

                                @if($p->status_pendaftaran === 'disetujui')

                                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">

                                        Disetujui

                                    </span>

                                @elseif($p->status_pendaftaran === 'ditolak')

                                    <span class="text-xs font-bold text-rose-600 dark:text-rose-400" title="{{ $p->catatan_admin }}">

                                        Ditolak

                                    </span>

                                @else

                                    <span class="text-xs font-bold text-amber-600 dark:text-amber-400">

                                        Menunggu Review

                                    </span>

                                @endif

                            </td>



                            <!-- Pembayaran (Pure Colored Text - Rule #14) -->

                            <td class="px-6 py-4 space-y-1">

                                @if($p->status_pembayaran === 'gratis')

                                    <span class="text-xs font-medium text-gray-600 dark:text-gray-400">

                                        Gratis (Free)

                                    </span>

                                @elseif($p->status_pembayaran === 'lunas')

                                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">

                                        Lunas

                                    </span>

                                @elseif($p->status_pembayaran === 'menunggu_konfirmasi')

                                    <div class="space-y-1">

                                        <span class="text-xs font-bold text-blue-600 dark:text-blue-400 block">

                                            Bukti Terunggah

                                        </span>

                                        @if($p->bukti_transfer)

                                            <div>

                                                <button type="button" @click="transferProofUrl = '{{ $p->bukti_transfer }}'; transferProofModal = true;" class="text-[11px] text-amber-600 dark:text-amber-400 font-extrabold hover:underline cursor-pointer">

                                                    Lihat Struk TF

                                                </button>

                                            </div>

                                        @endif

                                    </div>

                                @else

                                    <span class="text-xs font-bold text-amber-600 dark:text-amber-400">

                                        Menunggu Bayar

                                    </span>

                                @endif

                            </td>



                            <!-- Karya Dikirim -->

                            <td class="px-6 py-4">

                                <span class="font-extrabold">{{ $p->submissions->count() }}</span>

                                <span class="text-gray-400 text-[11px]">/ {{ $contest->max_submissions }} Karya</span>

                            </td>



                            <!-- Aksi Admin -->

                            <td class="px-6 py-4 text-right">

                                <div class="flex items-center justify-end gap-2">

                                    <!-- Jika Masih Menunggu Review -->

                                    @if($p->status_pendaftaran === 'menunggu_verifikasi')

                                        <button type="button" 

                                                @click="$dispatch('open-confirm-modal', {

                                                    title: 'Setujui Calon Peserta?',

                                                    message: 'Konfirmasi pendaftaran peserta {{ addslashes($p->nama_lengkap) }} untuk mengikuti event lomba ini.',

                                                    buttonText: 'Ya, Terima Peserta',

                                                    iconType: 'check',

                                                    buttonClass: 'bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold',

                                                    actionUrl: '{{ route('admin.contests.participant.approve', $p->id) }}',

                                                    method: 'POST'

                                                })"

                                                class="px-3 py-1.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-extrabold text-[11px] shadow-2xs transition cursor-pointer">

                                            Terima

                                        </button>



                                        <button type="button" @click="rejectUrl = '{{ route('admin.contests.participant.reject', $p->id) }}'; rejectName = '{{ addslashes($p->nama_lengkap) }}'; rejectModal = true;" class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 font-extrabold text-[11px] border border-rose-200 transition cursor-pointer">

                                            Tolak

                                        </button>

                                    @elseif($p->status_pembayaran === 'menunggu_konfirmasi')

                                        <!-- Verifikasi Pembayaran -->

                                        <button type="button" 

                                                @click="$dispatch('open-confirm-modal', {

                                                    title: 'Konfirmasi Pembayaran?',

                                                    message: 'Verifikasi bahwa bukti transfer peserta {{ addslashes($p->nama_lengkap) }} telah valid dan lunas.',

                                                    buttonText: 'Ya, Konfirmasi Lunas',

                                                    iconType: 'check',

                                                    buttonClass: 'bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold',

                                                    actionUrl: '{{ route('admin.contests.participant.verify_payment', $p->id) }}',

                                                    method: 'POST'

                                                })"

                                                class="px-3 py-1.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-extrabold text-[11px] shadow-2xs transition cursor-pointer">

                                            Konfirmasi Lunas

                                        </button>

                                    @else

                                        <span class="text-[11px] text-gray-400 font-medium">Selesai</span>

                                    @endif

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="7" class="px-6 py-12 text-center text-gray-400 font-bold">

                                Belum ada calon peserta yang mendaftar pada event lomba ini.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>



    <!-- Modal Tolak Peserta -->

    <div x-show="rejectModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">

        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="rejectModal = false"></div>

        <div class="flex min-h-full items-center justify-center p-4">

            <div class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-3xl p-6 sm:p-7 shadow-2xl border border-gray-200 dark:border-gray-700 space-y-4">

                <div class="text-center space-y-1">

                    <h3 class="text-base font-black text-gray-900 dark:text-white text-center">Tolak Pendaftaran Peserta</h3>

                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 text-center">Calon Peserta: <span class="font-bold text-gray-800 dark:text-white" x-text="rejectName"></span></p>

                </div>



                <form :action="rejectUrl" 
                      method="POST" 
                      novalidate
                      class="space-y-4"
                      x-data="{
                        catatan: '',
                        touched: false,
                        error: '',
                        validate() {
                            if (!this.catatan || !this.catatan.trim()) {
                                this.error = 'Alasan penolakan wajib diisi.';
                                return false;
                            }
                            if (this.catatan.trim().length < 5) {
                                this.error = 'Alasan penolakan minimal 5 karakter.';
                                return false;
                            }
                            this.error = '';
                            return true;
                        },
                        submitForm(e) {
                            this.touched = true;
                            if (!this.validate()) {
                                e.preventDefault();
                                this.$refs.catatanInput?.focus();
                                return false;
                            }
                        }
                      }"
                      @submit="submitForm($event)">

                    @csrf

                    <div>

                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Alasan Penolakan <span class="text-rose-500">*</span></label>

                        <textarea name="catatan_admin" 
                                  x-ref="catatanInput"
                                  x-model="catatan"
                                  @input="touched = true; validate()"
                                  @blur="touched = true; validate()"
                                  rows="3" 
                                  placeholder="Contoh: Berkas portofolio belum sesuai syarat ketentuan..." 
                                  class="w-full px-4 py-2.5 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none resize-none focus:ring-2 transition"
                                  :class="touched && error ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500 focus:border-amber-500'"></textarea>
                        <p x-show="touched && error" x-text="error" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>

                    </div>



                    <div class="flex items-center justify-center gap-3 pt-2">

                        <button type="button" @click="rejectModal = false; catatan = ''; error = ''; touched = false" class="px-5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-bold text-xs hover:bg-gray-50 transition cursor-pointer">Batal</button>

                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-extrabold text-xs shadow-xs transition cursor-pointer">Tolak Peserta</button>

                    </div>

                </form>

            </div>

        </div>

    </div>



    <!-- Modal Preview Struk Bukti Transfer -->

    <div x-show="transferProofModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">

        <div class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm" @click="transferProofModal = false"></div>

        <div class="flex min-h-full items-center justify-center p-4">

            <div class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-2xl border border-gray-200 dark:border-gray-700 space-y-4 text-center">

                <h3 class="text-base font-black text-gray-900 dark:text-white text-center">Bukti Transfer Pembayaran</h3>

                <div class="max-h-[65vh] overflow-auto rounded-2xl border border-gray-100 dark:border-gray-700">

                    <img :src="transferProofUrl" alt="Struk Bukti Transfer" class="w-full h-auto object-contain mx-auto" />

                </div>

                <div class="flex items-center justify-center pt-2">

                    <button type="button" 

                            @click="transferProofModal = false" 

                            class="px-5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-xs font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 transition cursor-pointer shadow-2xs">

                        Tutup

                    </button>

                </div>

            </div>

        </div>

    </div>



</div>

@endsection

