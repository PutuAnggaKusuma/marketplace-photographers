@extends('layouts.photographer')







@section('title', 'Kelola Tanya Jawab (FAQ) - Fotografer')







@section('content')



<div x-data="{
    faqModalOpen: false,
    isEdit: false,
    formAction: '{{ route('photographer.faqs.store') }}',
    formData: {
        id: null,
        pertanyaan: '',
        jawaban: '',
        urutan: 1,
        is_active: 1
    },
    touched: {
        pertanyaan: false,
        jawaban: false,
        urutan: false
    },
    errors: {
        pertanyaan: '',
        jawaban: '',
        urutan: ''
    },
    validatePertanyaan() {
        if (!this.formData.pertanyaan || this.formData.pertanyaan.trim() === '') {
            this.errors.pertanyaan = 'Pertanyaan FAQ wajib diisi.';
        } else if (this.formData.pertanyaan.trim().length < 5) {
            this.errors.pertanyaan = 'Pertanyaan FAQ minimal 5 karakter.';
        } else {
            this.errors.pertanyaan = '';
        }
    },
    validateJawaban() {
        if (!this.formData.jawaban || this.formData.jawaban.trim() === '') {
            this.errors.jawaban = 'Jawaban FAQ wajib diisi.';
        } else if (this.formData.jawaban.trim().length < 10) {
            this.errors.jawaban = 'Jawaban FAQ minimal 10 karakter.';
        } else {
            this.errors.jawaban = '';
        }
    },
    validateUrutan() {
        if (!this.formData.urutan || Number(this.formData.urutan) < 1) {
            this.errors.urutan = 'Urutan tampil minimal 1.';
        } else {
            this.errors.urutan = '';
        }
    },
    openCreateModal() {
        this.isEdit = false;
        this.formAction = '{{ route('photographer.faqs.store') }}';
        this.formData = {
            id: null,
            pertanyaan: '',
            jawaban: '',
            urutan: {{ $faqs->count() + 1 }},
            is_active: 1
        };
        this.touched = { pertanyaan: false, jawaban: false, urutan: false };
        this.errors = { pertanyaan: '', jawaban: '', urutan: '' };
        this.faqModalOpen = true;
    },
    openEditModal(faq) {
        this.isEdit = true;
        this.formAction = '/photographer/faqs/' + faq.id + '/update';
        this.formData = {
            id: faq.id,
            pertanyaan: faq.pertanyaan,
            jawaban: faq.jawaban,
            urutan: faq.urutan,
            is_active: faq.is_active ? 1 : 0
        };
        this.touched = { pertanyaan: false, jawaban: false, urutan: false };
        this.errors = { pertanyaan: '', jawaban: '', urutan: '' };
        this.faqModalOpen = true;
    },
    closeModal() {
        this.faqModalOpen = false;
    },
    submitFaqForm(e) {
        this.touched.pertanyaan = true;
        this.touched.jawaban = true;
        this.touched.urutan = true;

        this.validatePertanyaan();
        this.validateJawaban();
        this.validateUrutan();

        let firstInvalidRef = null;
        if (this.errors.pertanyaan) firstInvalidRef = firstInvalidRef || this.$refs.fieldPertanyaan;
        if (this.errors.urutan) firstInvalidRef = firstInvalidRef || this.$refs.fieldUrutan;
        if (this.errors.jawaban) firstInvalidRef = firstInvalidRef || this.$refs.fieldJawaban;

        if (this.errors.pertanyaan || this.errors.jawaban || this.errors.urutan) {
            e.preventDefault();
            if (firstInvalidRef) {
                firstInvalidRef.focus();
                firstInvalidRef.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    }
}" @if(isset($errors) && $errors->any()) x-init="$nextTick(() => $dispatch('show-toast', { type: 'error', message: {{ \Illuminate\Support\Js::from($errors->first()) }} }))" @endif class="space-y-6">







    <!-- Header Section -->



    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">



        <div>



            <h1 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">Tanya Jawab (FAQ) Studio</h1>



            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Kelola pertanyaan umum seputar layanan, fasilitas, dan ketentuan pemotretan yang tampil di profil publik Anda.</p>



        </div>







        <button type="button" 



                @click="openCreateModal()" 



                class="px-5 py-2.5 bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs rounded-xl shadow-xs transition flex items-center gap-2 cursor-pointer shrink-0 self-start sm:self-auto">



            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>



            <span>+ Tambah Pertanyaan FAQ</span>



        </button>



    </div>







    <!-- Metric Stat Cards -->



    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">



        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-xs space-y-1.5">



            <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Total Pertanyaan</span>



            <div class="text-lg sm:text-xl font-black text-gray-900 dark:text-white">{{ $faqs->count() }} Pertanyaan</div>



        </div>







        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-xs space-y-1.5">



            <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Status Tayang</span>



            <div class="text-lg sm:text-xl font-black text-emerald-600 dark:text-emerald-400">{{ $faqs->where('is_active', true)->count() }} Aktif</div>



        </div>







        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-xs space-y-1.5">



            <span class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Pratinjau Publik</span>



            <a href="{{ route('public.photographers.show', $photographer->id) }}" target="_blank" class="text-xs font-black text-amber-600 dark:text-amber-400 hover:underline inline-flex items-center gap-1.5 pt-1">



                <span>Lihat Tab FAQ di Profil Publik</span>



                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>



            </a>



        </div>



    </div>







    <!-- Table Container -->



    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-xs overflow-hidden">



        <div class="p-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">



            <h2 class="text-sm font-extrabold text-gray-900 dark:text-white uppercase tracking-wider">Daftar Pertanyaan & Jawaban FAQ</h2>



        </div>







        <div class="overflow-x-auto">



            <table class="w-full text-left border-collapse">



                <thead>



                    <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 text-[11px] font-extrabold uppercase tracking-wider text-gray-500 dark:text-gray-400">



                        <th class="py-3.5 px-4 sm:px-6 w-1/12 text-center">Urutan</th>



                        <th class="py-3.5 px-4 sm:px-6 w-5/12">Pertanyaan</th>



                        <th class="py-3.5 px-4 sm:px-6 w-4/12">Jawaban Singkat</th>



                        <th class="py-3.5 px-4 sm:px-6 w-1/12 text-center">Status</th>



                        <th class="py-3.5 px-4 sm:px-6 w-1/12 text-center">Aksi</th>



                    </tr>



                </thead>



                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60 text-xs">



                    @forelse($faqs as $faq)



                        <tr class="hover:bg-amber-50/30 dark:hover:bg-amber-950/20 transition">



                            <td class="py-4 px-4 sm:px-6 text-center font-mono font-bold text-gray-700 dark:text-gray-300">



                                {{ $faq->urutan }}



                            </td>



                            <td class="py-4 px-4 sm:px-6 font-bold text-gray-900 dark:text-white">



                                {{ $faq->pertanyaan }}



                            </td>



                            <td class="py-4 px-4 sm:px-6 text-gray-500 dark:text-gray-400 leading-relaxed line-clamp-2">



                                {{ $faq->jawaban }}



                            </td>



                            <td class="py-4 px-4 sm:px-6 text-center whitespace-nowrap">



                                @if($faq->is_active)



                                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">Aktif</span>



                                @else



                                    <span class="text-xs font-bold text-gray-400">Nonaktif</span>



                                @endif



                            </td>



                            <!-- Action Buttons: Strict Rule #80 (Trio Baku: Edit & Hapus) -->



                            <td class="py-4 px-4 sm:px-6 text-center whitespace-nowrap">



                                <div class="flex items-center justify-center gap-1.5">



                                    <button type="button" 



                                            @click="openEditModal({{ json_encode($faq) }})"



                                            class="px-3 py-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200/80 dark:border-amber-900/60 font-bold text-xs transition min-w-[60px] text-center cursor-pointer">



                                        Edit



                                    </button>







                                    <button type="button" 



                                            @click="$dispatch('open-confirm-modal', {



                                                title: 'Hapus Pertanyaan FAQ?',



                                                message: 'Apakah Anda yakin ingin menghapus pertanyaan FAQ ini? Data yang dihapus tidak dapat dikembalikan.',



                                                confirmText: 'Ya, Hapus Data',



                                                type: 'danger',



                                                actionUrl: '{{ route('photographer.faqs.destroy', $faq->id) }}',



                                                method: 'POST'



                                            })"



                                            class="px-3 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200/80 dark:border-rose-900/60 font-bold text-xs transition min-w-[60px] text-center cursor-pointer">



                                        Hapus



                                    </button>



                                </div>



                            </td>



                        </tr>



                    @empty



                        <tr>



                            <td colspan="5" class="py-12 text-center text-gray-400 text-xs font-medium space-y-3">



                                <svg class="w-10 h-10 mx-auto text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"/></svg>



                                <p>Belum ada pertanyaan FAQ yang dibuat.</p>



                                <button type="button" @click="openCreateModal()" class="px-4 py-2 bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs rounded-xl transition">



                                    + Buat Pertanyaan Pertama



                                </button>



                            </td>



                        </tr>



                    @endforelse



                </tbody>



            </table>



        </div>



    </div>







    <!-- Modal Form (Rule #59, #73, #74, #76 Compliant) -->



    <div x-show="faqModalOpen" 



         x-cloak 



         class="fixed inset-0 z-50 overflow-y-auto"



         aria-labelledby="faq-modal-title" role="dialog" aria-modal="true">



        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">



            <!-- Backdrop -->



            <div x-show="faqModalOpen"



                 x-transition:enter="ease-out duration-300"



                 x-transition:enter-start="opacity-0"



                 x-transition:enter-end="opacity-100"



                 x-transition:leave="ease-in duration-200"



                 x-transition:leave-start="opacity-100"



                 x-transition:leave-end="opacity-0"



                 @click="closeModal()"



                 class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity"></div>







            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>







            <!-- Modal Content (Centered Title without Icon, Max 2 Buttons Centered, No top-right X) -->



            <div x-show="faqModalOpen"



                 x-transition:enter="ease-out duration-300"



                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"



                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"



                 x-transition:leave="ease-in duration-200"



                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"



                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"



                 class="relative inline-block align-bottom bg-white dark:bg-gray-850 rounded-2xl p-6 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100 dark:border-gray-700">



                



                <!-- Centered Title (NO ICON, Rule #59) -->



                <h3 class="text-base sm:text-lg font-black text-gray-900 dark:text-white text-center" id="faq-modal-title" x-text="isEdit ? 'Edit Pertanyaan FAQ' : 'Tambah Pertanyaan FAQ'"></h3>



                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 text-center max-w-sm mx-auto">



                    Tuliskan pertanyaan umum yang sering ditanyakan oleh klien dan sertakan jawaban jelas.



                </p>







                <!-- Form (Rule #73: 2-column grid for short fields, full-width textarea) -->



                <form :action="formAction" method="POST" @submit="submitFaqForm($event)" novalidate class="mt-5 space-y-4">

                    @csrf

                    

                    <!-- Pertanyaan -->

                    <div>

                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">

                            Pertanyaan <span class="text-rose-500">*</span>

                        </label>

                        <input type="text" 
                               name="pertanyaan" 
                               x-ref="fieldPertanyaan"
                               x-model="formData.pertanyaan" 
                               @input="touched.pertanyaan = true; validatePertanyaan()"
                               @blur="touched.pertanyaan = true; validatePertanyaan()"
                               placeholder="Contoh: Berapa lama estimasi waktu serah terima hasil foto?"
                               class="w-full px-3.5 py-2.5 rounded-xl border bg-gray-50 dark:bg-gray-800 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition"
                               :class="touched.pertanyaan && errors.pertanyaan ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500 focus:border-amber-500'">
                        <p x-show="touched.pertanyaan && errors.pertanyaan" x-text="errors.pertanyaan" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>

                    </div>



                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">

                        <!-- Urutan Tampil -->

                        <div>

                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">

                                Urutan Tampil <span class="text-rose-500">*</span>

                            </label>

                            <input type="number" 
                                   name="urutan" 
                                   x-ref="fieldUrutan"
                                   x-model="formData.urutan" 
                                   @input="touched.urutan = true; validateUrutan()"
                                   @blur="touched.urutan = true; validateUrutan()"
                                   min="1" 
                                   max="999"
                                   class="w-full px-3.5 py-2.5 rounded-xl border bg-gray-50 dark:bg-gray-800 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition"
                                   :class="touched.urutan && errors.urutan ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500 focus:border-amber-500'">
                            <p x-show="touched.urutan && errors.urutan" x-text="errors.urutan" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>

                        </div>







                        <!-- Status Aktif (Edit Only - Alpine Custom Dropdown per Rule #11) -->
                        <div x-show="isEdit" x-data="{ openStatusDropdown: false }">
                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">
                                Status Tampilan
                            </label>
                            <input type="hidden" name="is_active" :value="formData.is_active">
                            <div class="relative">
                                <button type="button"
                                        @click="openStatusDropdown = !openStatusDropdown"
                                        class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-xs font-bold text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-750 transition cursor-pointer">
                                    <span x-text="formData.is_active == 1 ? 'Aktif (Tayang)' : 'Nonaktif (Sembunyikan)'"></span>
                                    <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 shrink-0" :class="{ 'rotate-180': openStatusDropdown }" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                <div x-show="openStatusDropdown"
                                     @click.outside="openStatusDropdown = false"
                                     x-transition
                                     class="absolute z-50 top-full mt-1.5 left-0 w-full bg-white dark:bg-gray-900 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 p-1 space-y-0.5 overflow-hidden"
                                     style="display: none;">
                                    <button type="button"
                                            @click="formData.is_active = 1; openStatusDropdown = false"
                                            class="w-full text-left px-3 py-2 text-xs font-bold rounded-lg text-gray-800 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-700 dark:hover:text-amber-300 transition cursor-pointer"
                                            :class="formData.is_active == 1 ? 'bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 font-black' : ''">
                                        Aktif (Tayang)
                                    </button>
                                    <button type="button"
                                            @click="formData.is_active = 0; openStatusDropdown = false"
                                            class="w-full text-left px-3 py-2 text-xs font-bold rounded-lg text-gray-800 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-700 dark:hover:text-amber-300 transition cursor-pointer"
                                            :class="formData.is_active == 0 ? 'bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 font-black' : ''">
                                        Nonaktif (Sembunyikan)
                                    </button>
                                </div>
                            </div>
                        </div>



                    </div>







                    <!-- Jawaban (Expanded Textarea, Rule #73) -->



                    <div>



                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">



                            Jawaban Lengkap <span class="text-rose-500">*</span>



                        </label>



                        <textarea name="jawaban" 
                                  x-ref="fieldJawaban"
                                  x-model="formData.jawaban" 
                                  @input="touched.jawaban = true; validateJawaban()"
                                  @blur="touched.jawaban = true; validateJawaban()"
                                  rows="4" 
                                  placeholder="Tuliskan jawaban yang rinci, sopan, dan mudah dipahami oleh klien..."
                                  class="w-full px-3.5 py-2.5 rounded-xl border bg-gray-50 dark:bg-gray-800 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition resize-none"
                                  :class="touched.jawaban && errors.jawaban ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500 focus:border-amber-500'"></textarea>
                        <p x-show="touched.jawaban && errors.jawaban" x-text="errors.jawaban" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>



                    </div>







                    <!-- Action Buttons: Centered, Batal on Left, Standard Labels (Rule #59, #74, #76) -->



                    <div class="pt-3 flex items-center justify-center gap-3">



                        <button type="button" 



                                @click="closeModal()"



                                class="px-5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-750 text-gray-700 dark:text-gray-300 font-bold text-xs transition cursor-pointer">



                            Batal



                        </button>



                        <button type="submit" 



                                class="px-5 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs shadow-xs transition cursor-pointer">



                            <span x-text="isEdit ? 'Perbaharui Data' : 'Simpan Data'"></span>



                        </button>



                    </div>



                </form>



            </div>



        </div>



    </div>







</div>



@endsection







