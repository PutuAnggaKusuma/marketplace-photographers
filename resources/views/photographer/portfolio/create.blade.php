@extends('layouts.photographer')

@section('title', 'Tambah Portofolio — Fotografer')

@section('content')
<div class="space-y-6">

    <!-- Header Section (Rule #51 & Rule #22) -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <!-- Breadcrumb Navigation -->
            <nav class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 mb-1.5">
                <a href="{{ route('photographer.portfolio') }}" class="hover:text-amber-500 transition">Portofolio</a>
                <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                <span class="text-gray-900 dark:text-white font-bold">Tambah Portofolio</span>
            </nav>
            <h1 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">
                Tambah Portofolio
            </h1>
        </div>
        <div>
            <a href="{{ route('photographer.portfolio') }}" class="px-4 py-2 rounded-xl bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 font-bold text-xs transition inline-flex items-center shadow-2xs shrink-0 cursor-pointer">Kembali ke Portofolio</a>
        </div>
    </div>

    <!-- Main Form Card (Rule #73 & Rule #21) -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm p-6 sm:p-8"
         x-data="portfolioFormApp()">
        
        <form action="{{ route('photographer.portfolio.store') }}" method="POST" enctype="multipart/form-data" @submit="submitForm($event)" novalidate class="space-y-5">
            @csrf

            <!-- 1. Judul Portofolio & 2. Kategori (Horizontal 2 Kolom, Rule #73) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                        Judul Portofolio <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           name="judul" 
                           x-ref="fieldJudul"
                           x-model="form.judul"
                           @input="touched.judul = true; validateJudul()"
                           @blur="touched.judul = true; validateJudul()"
                           placeholder="Contoh: Golden Hour Sunset Session di Melasti Beach" 
                           class="w-full px-4 py-3 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition"
                           :class="touched.judul && errors.judul ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500 focus:border-amber-500'">
                    <p x-show="touched.judul && errors.judul" x-text="errors.judul" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                        Kategori <span class="text-rose-500">*</span>
                    </label>
                    @php
                        $selectedCatId = old('id_kategori', '');
                        $selectedCatName = '';
                        foreach($categories as $cat) {
                            if ($cat->id == $selectedCatId) {
                                $selectedCatName = $cat->nama_kategori;
                                break;
                            }
                        }
                        if (!$selectedCatName) {
                            $selectedCatName = '-- Pilih Kategori --';
                        }
                    @endphp
                    <div class="relative w-full">
                        <input type="hidden" name="id_kategori" :value="form.id_kategori">
                        <button @click="catOpen = !catOpen" type="button" 
                                x-ref="fieldKategori"
                                class="w-full px-4 py-3 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs font-bold flex items-center justify-between gap-2 shadow-2xs cursor-pointer transition outline-none"
                                :class="touched.id_kategori && errors.id_kategori ? 'border-red-400 focus:ring-red-400' : 'border-gray-200 dark:border-gray-700 hover:border-amber-400 dark:hover:border-amber-500'">
                            <span x-text="form.selectedCatName" :class="form.id_kategori ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-gray-500'" class="truncate"></span>
                            <svg class="w-4 h-4 text-gray-400 shrink-0 transition-transform duration-200" :class="catOpen ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="catOpen" @click.away="catOpen = false" x-transition 
                             class="absolute z-50 left-0 mt-2 w-full bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700/80 py-1 max-h-60 overflow-y-auto" style="display: none;">
                            @foreach($categories as $cat)
                                <button type="button" 
                                        @click="selectCategory('{{ $cat->id }}', '{{ addslashes($cat->nama_kategori) }}')" 
                                        class="w-full px-4 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition flex items-center justify-between text-left cursor-pointer border-b border-gray-50 dark:border-gray-700/40 last:border-0"
                                        :class="form.id_kategori == '{{ $cat->id }}' ? 'bg-amber-50/70 dark:bg-amber-950/30 text-amber-600 font-extrabold' : ''">
                                    <span>{{ $cat->nama_kategori }}</span>
                                    <span x-show="form.id_kategori == '{{ $cat->id }}'" class="shrink-0"><svg class="w-4 h-4 text-amber-600 dark:text-amber-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                    <p x-show="touched.id_kategori && errors.id_kategori" x-text="errors.id_kategori" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
                </div>
            </div>

            <!-- 3. Deskripsi Portofolio (Vertikal Full-Width, Rule #73) -->
            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                    Deskripsi <span class="text-rose-500">*</span>
                </label>
                <textarea name="deskripsi" 
                          x-ref="fieldDeskripsi"
                          x-model="form.deskripsi"
                          @input="touched.deskripsi = true; validateDeskripsi()"
                          @blur="touched.deskripsi = true; validateDeskripsi()"
                          rows="6" 
                          placeholder="Tuliskan deskripsi konsep pemotretan, lokasi, atau detail karya ini..." 
                          class="w-full min-h-[150px] px-4 py-3 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition resize-y leading-relaxed"
                          :class="touched.deskripsi && errors.deskripsi ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500 focus:border-amber-500'">{{ old('deskripsi') }}</textarea>
                <p x-show="touched.deskripsi && errors.deskripsi" x-text="errors.deskripsi" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
                @error('deskripsi')
                    <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- 4. Upload Foto Portofolio (Vertikal Full-Width, Rule #73 & Rule #21) -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">
                        Foto Portofolio <span class="text-rose-500">*</span>
                    </label>
                    <span class="text-xs text-gray-500 font-medium" 
                          x-text="fileCount > 0 ? (fileCount + ' foto dipilih ' + (fileCount >= 4 ? '(Terpenuhi)' : '(kurang ' + (4 - fileCount) + ')')) : 'Minimal 4 foto'"></span>
                </div>

                <div class="space-y-3">
                    <div class="p-5 rounded-2xl bg-gray-50 dark:bg-gray-900 border-2 border-dashed transition text-center"
                         :class="(touched.foto && errors.foto) || uploadNotice ? 'border-red-400' : 'border-gray-200 dark:border-gray-700'">
                        <label class="cursor-pointer inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs shadow-xs transition my-1">
                            <span>Pilih Minimal 4 Foto Portofolio</span>
                            <input type="file" 
                                   name="foto_files[]" 
                                   x-ref="fieldFoto"
                                   multiple 
                                   accept="image/png, image/jpeg, image/webp" 
                                   @change="filesChosen" 
                                   class="sr-only">
                        </label>
                        <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium mt-1">Pilih minimal 4 file foto sekaligus (tahan tombol Ctrl/Cmd untuk memilih banyak file). Format: JPG, PNG, WEBP maks. 20 MB per file.</p>
                    </div>

                    <!-- Multiple Preview Grid -->
                    <template x-if="previewUrls.length > 0">
                        <div class="flex flex-wrap gap-2.5 sm:gap-3 pt-2">
                            <template x-for="(url, idx) in previewUrls" :key="idx">
                                <div class="relative w-20 h-20 sm:w-24 sm:h-24 rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-2xs shrink-0">
                                    <img :src="url" class="w-full h-full object-cover">
                                    <span class="absolute top-1 left-1 px-1.5 py-0.5 rounded-md bg-black/75 text-[10px] font-black text-white leading-none" x-text="idx === 0 ? 'Utama' : (idx + 1)"></span>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
                <p x-show="uploadNotice" x-text="uploadNotice" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
                <p x-show="touched.foto && errors.foto" x-text="errors.foto" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
                @error('foto_files')
                    <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                @enderror
                @error('foto_files.*')
                    <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action Button Footer (Rule #74: Grouping Rata Kanan, Batal di Samping Kiri Simpan Data, Rule #41: No border-t Divider) -->
            <div class="flex items-center justify-end gap-3 pt-4">
                <a href="{{ route('photographer.portfolio') }}" class="px-5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold text-xs transition text-center shadow-2xs cursor-pointer">
                    Batal
                </a>
                <button type="submit" 
                        class="px-6 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs shadow-xs transition cursor-pointer">
                    Simpan Data
                </button>
            </div>

        </form>

    </div>

</div>

<script>
function portfolioFormApp() {
    return {
        previewUrls: [],
        fileCount: 0,
        uploadNotice: '',
        catOpen: false,
        form: {
            judul: @js(old('judul', '')),
            id_kategori: @js(old('id_kategori', '')),
            selectedCatName: @js($selectedCatName ?? '-- Pilih Kategori --'),
            deskripsi: @js(old('deskripsi', ''))
        },
        touched: {
            judul: {{ $errors->has('judul') ? 'true' : 'false' }},
            id_kategori: {{ $errors->has('id_kategori') ? 'true' : 'false' }},
            deskripsi: {{ $errors->has('deskripsi') ? 'true' : 'false' }},
            foto: {{ $errors->has('foto_files') ? 'true' : 'false' }}
        },
        errors: {
            judul: '{{ $errors->first('judul') ?: '' }}',
            id_kategori: '{{ $errors->first('id_kategori') ?: '' }}',
            deskripsi: '{{ $errors->first('deskripsi') ?: '' }}',
            foto: '{{ $errors->first('foto_files') ?: '' }}'
        },
        filesChosen(event) {
            const res = window.validateUploadFiles(event.target, {
                maxMB: 20,
                multiple: true
            });
            this.uploadNotice = res.errorMessage;
            const files = res.validFiles;
            this.fileCount = files.length;
            this.previewUrls = files.slice(0, 12).map(f => URL.createObjectURL(f));
            this.touched.foto = true;
            this.validateFoto();
        },
        selectCategory(id, name) {
            this.form.id_kategori = id;
            this.form.selectedCatName = name;
            this.catOpen = false;
            this.touched.id_kategori = true;
            this.validateCategory();
        },
        validateJudul() {
            if (!this.form.judul || this.form.judul.trim() === '') {
                this.errors.judul = 'Judul portofolio wajib diisi.';
            } else if (this.form.judul.trim().length < 3) {
                this.errors.judul = 'Judul portofolio minimal 3 karakter.';
            } else {
                this.errors.judul = '';
            }
        },
        validateCategory() {
            if (!this.form.id_kategori) {
                this.errors.id_kategori = 'Kategori portofolio wajib dipilih.';
            } else {
                this.errors.id_kategori = '';
            }
        },
        validateDeskripsi() {
            if (!this.form.deskripsi || this.form.deskripsi.trim() === '') {
                this.errors.deskripsi = 'Deskripsi portofolio wajib diisi.';
            } else if (this.form.deskripsi.trim().length < 15) {
                this.errors.deskripsi = 'Deskripsi minimal 15 karakter.';
            } else {
                this.errors.deskripsi = '';
            }
        },
        validateFoto() {
            if (this.fileCount < 4) {
                this.errors.foto = 'Wajib memilih minimal 4 file foto portofolio.';
            } else {
                this.errors.foto = '';
            }
        },
        submitForm(e) {
            this.touched.judul = true;
            this.touched.id_kategori = true;
            this.touched.deskripsi = true;
            this.touched.foto = true;

            this.validateJudul();
            this.validateCategory();
            this.validateDeskripsi();
            this.validateFoto();

            let firstInvalidRef = null;
            if (this.errors.judul) firstInvalidRef = firstInvalidRef || this.$refs.fieldJudul;
            if (this.errors.id_kategori) firstInvalidRef = firstInvalidRef || this.$refs.fieldKategori;
            if (this.errors.deskripsi) firstInvalidRef = firstInvalidRef || this.$refs.fieldDeskripsi;
            if (this.errors.foto) firstInvalidRef = firstInvalidRef || this.$refs.fieldFoto;

            if (this.errors.judul || this.errors.id_kategori || this.errors.deskripsi || this.errors.foto) {
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
