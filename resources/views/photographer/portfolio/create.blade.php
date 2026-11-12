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
                        $categoriesCount = $categories->count();
                        $defaultCatId = $categoriesCount === 1 ? $categories->first()->id : '';
                        $rawCatId = old('id_kategori', $defaultCatId);
                        $selectedCatId = '';
                        $selectedCatName = '';
                        foreach($categories as $cat) {
                            if ((string) $cat->id === (string) $rawCatId) {
                                $selectedCatId = $cat->id;
                                $selectedCatName = $cat->nama_kategori;
                                break;
                            }
                        }
                        if (!$selectedCatName) {
                            $selectedCatName = $categoriesCount === 0 ? 'Belum ada kategori di profil' : '-- Pilih Kategori --';
                        }
                    @endphp
                    <div class="relative w-full">
                        <input type="hidden" name="id_kategori" :value="form.id_kategori">
                        <button @click="if (hasCategories) catOpen = !catOpen" type="button" 
                                x-ref="fieldKategori"
                                :disabled="!hasCategories"
                                class="w-full px-4 py-3 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs font-bold flex items-center justify-between gap-2 shadow-2xs transition outline-none"
                                :class="!hasCategories
                                    ? 'opacity-60 cursor-not-allowed border-gray-200 dark:border-gray-700'
                                    : (touched.id_kategori && errors.id_kategori ? 'border-red-400 focus:ring-red-400 cursor-pointer' : 'border-gray-200 dark:border-gray-700 hover:border-amber-400 dark:hover:border-amber-500 cursor-pointer')">
                            <span x-text="form.selectedCatName" :class="form.id_kategori ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-gray-500'" class="truncate"></span>
                            <svg class="w-4 h-4 text-gray-400 shrink-0 transition-transform duration-200" :class="catOpen ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="catOpen && hasCategories" @click.away="catOpen = false" x-transition 
                             class="absolute z-50 left-0 mt-2 w-full bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700/80 py-1 max-h-60 overflow-y-auto" style="display: none;">
                            @foreach($categories as $cat)
                                <button type="button" 
                                        @click="selectCategory('{{ $cat->id }}', @js($cat->nama_kategori))" 
                                        class="w-full px-4 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition flex items-center justify-between text-left cursor-pointer border-b border-gray-50 dark:border-gray-700/40 last:border-0"
                                        :class="form.id_kategori == '{{ $cat->id }}' ? 'bg-amber-50/70 dark:bg-amber-950/30 text-amber-600 font-extrabold' : ''">
                                    <span>{{ $cat->nama_kategori }}</span>
                                    <span x-show="form.id_kategori == '{{ $cat->id }}'" class="shrink-0"><svg class="w-4 h-4 text-amber-600 dark:text-amber-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                    @if($categoriesCount === 0)
                        <p class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 mt-1">
                            Pilih kategori di <a href="{{ route('photographer.profile') }}" class="text-amber-600 dark:text-amber-400 font-bold hover:underline">Profil</a> terlebih dahulu.
                        </p>
                    @endif
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

            <!-- 4. Upload Foto Portofolio (Vertikal Full-Width, Rule #73, #22 & #97) -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">
                        Foto Portofolio <span class="text-rose-500">*</span>
                    </label>
                    <span class="text-xs font-semibold"
                          :class="fileCount >= 4 ? 'text-emerald-600 dark:text-emerald-400' : (touched.foto && errors.foto ? 'text-red-500' : 'text-gray-500 dark:text-gray-400')"
                          x-text="fileCount > 0 ? (fileCount + ' foto dipilih' + (fileCount >= 4 ? ' • Minimal terpenuhi' : ' (kurang ' + (4 - fileCount) + ' lagi)')) : 'Minimal 4 foto'"></span>
                </div>

                <div class="space-y-4">
                    <div x-ref="fotoDropzone"
                         tabindex="0"
                         role="button"
                         @click="$refs.fieldFoto.click()"
                         @keydown.enter.prevent="$refs.fieldFoto.click()"
                         @keydown.space.prevent="$refs.fieldFoto.click()"
                         @dragover.prevent="isDragging = true"
                         @dragleave.prevent="isDragging = false"
                         @drop.prevent="handleDrop($event)"
                         class="w-full min-h-[180px] sm:min-h-[200px] px-6 py-10 sm:py-12 rounded-2xl border-2 border-dashed flex flex-col items-center justify-center text-center gap-2.5 cursor-pointer transition outline-none"
                         :class="isDragging
                             ? 'border-amber-500 bg-amber-50/70 dark:bg-amber-950/25'
                             : ((touched.foto && errors.foto) || uploadNotice
                                 ? 'border-red-400 bg-red-50/40 dark:bg-red-950/20 focus:ring-2 focus:ring-red-400'
                                 : 'border-gray-200 dark:border-gray-700 bg-gray-50/80 dark:bg-gray-900/60 hover:border-amber-400 dark:hover:border-amber-500 hover:bg-amber-50/30 dark:hover:bg-amber-950/20 focus:ring-2 focus:ring-amber-500')">
                        <input type="file"
                               name="foto_files[]"
                               x-ref="fieldFoto"
                               multiple
                               accept="image/png, image/jpeg, image/webp"
                               @change="filesChosen"
                               class="sr-only">
                        <svg class="w-7 h-7 sm:w-8 sm:h-8 transition-colors" :class="isDragging ? 'text-amber-500' : 'text-gray-400 dark:text-gray-500'" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                        </svg>
                        <p class="text-xs sm:text-sm font-bold text-gray-700 dark:text-gray-200">
                            Seret dan lepas di sini
                        </p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 font-medium max-w-md leading-relaxed">
                            Minimal 4 foto sekaligus atau bertahap. Format JPG, PNG, WEBP maksimal 20 MB per file.
                        </p>
                    </div>

                    <!-- Multiple Preview Grid (Clean Cards + Lightbox + Per-File Remove) -->
                    <template x-if="selectedFiles.length > 0">
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 pt-1">
                            <template x-for="(item, idx) in selectedFiles" :key="item.key">
                                <div class="rounded-xl overflow-hidden bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-2xs flex flex-col">
                                    <div @click="openLightbox(idx)"
                                         class="relative aspect-[4/3] w-full overflow-hidden bg-gray-100 dark:bg-gray-900 cursor-pointer group">
                                        <img :src="item.url" :alt="item.name" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        <div class="absolute inset-0 bg-black/45 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                            <span class="text-xs font-bold text-white tracking-wide">Pratinjau</span>
                                        </div>
                                    </div>
                                    <div class="px-3 py-2 flex items-center justify-between gap-2 bg-white dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700/80">
                                        <span class="text-[11px] font-bold text-gray-700 dark:text-gray-300 truncate"
                                              x-text="(idx === 0 ? 'Utama' : 'Foto ' + (idx + 1)) + ' • ' + item.sizeText"></span>
                                        <button type="button"
                                                @click.stop="removeFile(idx)"
                                                class="text-[11px] font-bold text-rose-600 dark:text-rose-400 hover:underline shrink-0 cursor-pointer">
                                            Hapus
                                        </button>
                                    </div>
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

        <!-- Lightbox Pratinjau Foto (Rule #97) -->
        <div x-show="lightboxOpen"
             x-cloak
             @keydown.escape.window="lightboxOpen = false"
             @keydown.arrow-left.window="if (lightboxOpen) prevLightbox()"
             @keydown.arrow-right.window="if (lightboxOpen) nextLightbox()"
             @click.self="lightboxOpen = false"
             class="fixed inset-0 z-[9999] bg-black/90 backdrop-blur-md flex flex-col items-center justify-center p-4 sm:p-8">
            <button type="button"
                    @click="lightboxOpen = false"
                    aria-label="Tutup pratinjau"
                    class="absolute top-4 right-4 sm:top-6 sm:right-6 w-11 h-11 rounded-full bg-white/10 hover:bg-white/25 text-white flex items-center justify-center transition cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <img :src="lightboxUrl" :alt="lightboxCaption" class="max-w-full max-h-[78vh] object-contain rounded-2xl shadow-2xl border border-white/10 select-none">
            <div class="mt-4 flex flex-wrap items-center justify-center gap-3 px-4 py-2 rounded-full bg-white/10 text-xs font-semibold text-white/90">
                <button type="button" x-show="selectedFiles.length > 1" @click="prevLightbox()" class="hover:text-amber-300 transition cursor-pointer">Sebelumnya</button>
                <span x-text="lightboxCaption"></span>
                <button type="button" x-show="selectedFiles.length > 1" @click="nextLightbox()" class="hover:text-amber-300 transition cursor-pointer">Berikutnya</button>
            </div>
        </div>

    </div>

</div>

<script>
function portfolioFormApp() {
    return {
        selectedFiles: [],
        fileCount: 0,
        uploadNotice: '',
        isDragging: false,
        catOpen: false,
        hasCategories: {{ $categoriesCount > 0 ? 'true' : 'false' }},
        lightboxOpen: false,
        lightboxIdx: 0,
        lightboxUrl: '',
        lightboxCaption: '',
        form: {
            judul: @js(old('judul', '')),
            id_kategori: @js($selectedCatId),
            selectedCatName: @js($selectedCatName),
            deskripsi: @js(old('deskripsi', ''))
        },
        touched: {
            judul: {{ $errors->has('judul') ? 'true' : 'false' }},
            id_kategori: {{ $errors->has('id_kategori') ? 'true' : 'false' }},
            deskripsi: {{ $errors->has('deskripsi') ? 'true' : 'false' }},
            foto: {{ $errors->has('foto_files') ? 'true' : 'false' }}
        },
        errors: {
            judul: @js($errors->first('judul') ?: ''),
            id_kategori: @js($errors->first('id_kategori') ?: ''),
            deskripsi: @js($errors->first('deskripsi') ?: ''),
            foto: @js($errors->first('foto_files') ?: '')
        },
        filesChosen(event) {
            this.addFiles(event.target.files);
        },
        handleDrop(event) {
            this.isDragging = false;
            if (event.dataTransfer && event.dataTransfer.files) {
                this.addFiles(event.dataTransfer.files);
            }
        },
        addFiles(fileList) {
            const incoming = Array.from(fileList || []);
            if (incoming.length === 0) return;

            const res = window.validateUploadFiles(incoming, {
                maxMB: 20,
                multiple: true
            });
            this.uploadNotice = res.errorMessage;

            if (res.validFiles.length > 0) {
                const existingKeys = new Set(this.selectedFiles.map(item => item.key));
                for (const f of res.validFiles) {
                    const key = `${f.name}|${f.size}|${f.lastModified}`;
                    if (!existingKeys.has(key)) {
                        existingKeys.add(key);
                        this.selectedFiles.push({
                            key,
                            file: f,
                            url: URL.createObjectURL(f),
                            name: f.name,
                            sizeText: window.formatFileSizeMB(f.size)
                        });
                    }
                }
            }

            this.syncInputFiles();
            this.touched.foto = true;
            this.validateFoto();
        },
        syncInputFiles() {
            if (typeof DataTransfer !== 'undefined' && this.$refs.fieldFoto) {
                const dt = new DataTransfer();
                this.selectedFiles.forEach(item => dt.items.add(item.file));
                this.$refs.fieldFoto.files = dt.files;
            }
            this.fileCount = this.selectedFiles.length;
        },
        removeFile(idx) {
            const item = this.selectedFiles[idx];
            if (item && item.url) {
                URL.revokeObjectURL(item.url);
            }
            this.selectedFiles.splice(idx, 1);
            this.syncInputFiles();
            this.uploadNotice = '';
            this.touched.foto = true;
            this.validateFoto();
        },
        openLightbox(idx) {
            if (idx < 0 || idx >= this.selectedFiles.length) return;
            this.lightboxIdx = idx;
            this.updateLightboxView();
            this.lightboxOpen = true;
        },
        prevLightbox() {
            if (this.selectedFiles.length === 0) return;
            this.lightboxIdx = (this.lightboxIdx - 1 + this.selectedFiles.length) % this.selectedFiles.length;
            this.updateLightboxView();
        },
        nextLightbox() {
            if (this.selectedFiles.length === 0) return;
            this.lightboxIdx = (this.lightboxIdx + 1) % this.selectedFiles.length;
            this.updateLightboxView();
        },
        updateLightboxView() {
            const item = this.selectedFiles[this.lightboxIdx];
            if (!item) return;
            this.lightboxUrl = item.url;
            this.lightboxCaption = `${item.name} • Foto ${this.lightboxIdx + 1} dari ${this.selectedFiles.length}`;
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
            if (!this.hasCategories) {
                this.errors.id_kategori = 'Pilih kategori di profil terlebih dahulu.';
            } else if (!this.form.id_kategori) {
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
            if (this.errors.foto) firstInvalidRef = firstInvalidRef || this.$refs.fotoDropzone;

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
