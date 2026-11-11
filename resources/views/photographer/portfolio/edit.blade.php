@extends('layouts.photographer')

@section('title', 'Edit Portofolio — Fotografer')

@section('content')
<div class="space-y-6">

    <!-- Header Section (Rule #51 & Rule #22) -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <!-- Breadcrumb Navigation -->
            <nav class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 mb-1.5">
                <a href="{{ route('photographer.portfolio') }}" class="hover:text-amber-500 transition">Portofolio</a>
                <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                <span class="text-gray-900 dark:text-white font-bold">Edit Portofolio</span>
            </nav>
            <h1 class="text-lg sm:text-xl font-black text-gray-900 dark:text-white tracking-tight">
                Edit Portofolio
            </h1>
        </div>
        <div>
            <a href="{{ route('photographer.portfolio') }}" class="px-4 py-2 rounded-xl bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 font-bold text-xs transition inline-flex items-center shadow-2xs shrink-0 cursor-pointer">Kembali ke Portofolio</a>
        </div>
    </div>

    <!-- Main Vertical Form Card (Rule #73 & Rule #21) -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm p-6 sm:p-8"
         x-data="portfolioEditApp()">
        
        <form action="{{ route('photographer.portfolio.update', $portfolio->id) }}" method="POST" enctype="multipart/form-data" @submit="submitForm($event)" novalidate class="space-y-5">
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
                        $selectedCatId = old('id_kategori', $portfolio->id_kategori ?? '');
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
                          :class="touched.deskripsi && errors.deskripsi ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500 focus:border-amber-500'">{{ old('deskripsi', $portfolio->deskripsi) }}</textarea>
                <p x-show="touched.deskripsi && errors.deskripsi" x-text="errors.deskripsi" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
                @error('deskripsi')
                    <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- 4. Galeri Foto Yang Sudah Ada -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">
                        Foto Saat Ini ({{ $portfolio->medias->count() }} Foto)
                    </label>
                    <span class="text-xs font-medium text-gray-500">Minimal 4 foto</span>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                    @foreach($portfolio->medias as $med)
                        <div class="rounded-xl overflow-hidden bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-2xs flex flex-col">
                            <div @click="openDirectLightbox(@js($med->media), @js(($loop->iteration == 1 ? 'Utama' : 'Foto ' . $loop->iteration) . ' • Foto saat ini'))"
                                 class="relative aspect-[4/3] w-full overflow-hidden bg-gray-100 dark:bg-gray-900 cursor-pointer group">
                                <img src="{{ $med->media }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" alt="Foto Portofolio {{ $loop->iteration }}">
                                <div class="absolute inset-0 bg-black/45 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                    <span class="text-xs font-bold text-white tracking-wide">Pratinjau</span>
                                </div>
                            </div>
                            <div class="px-3 py-2 flex items-center justify-between gap-2 bg-white dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700/80">
                                <span class="text-[11px] font-bold text-gray-700 dark:text-gray-300">
                                    {{ $loop->iteration == 1 ? 'Utama' : 'Foto ' . $loop->iteration }}
                                </span>
                                <span class="text-[11px] text-gray-400 font-medium">Tersimpan</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- 5. Tambah Foto Baru ke Album (Rule #22 & #97) -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">
                        Tambah Foto (Opsional)
                    </label>
                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400"
                          x-show="newFilesCount > 0"
                          x-text="newFilesCount + ' foto baru dipilih'"
                          x-cloak></span>
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
                         class="w-full min-h-[160px] sm:min-h-[180px] px-6 py-10 sm:py-12 rounded-2xl border-2 border-dashed flex flex-col items-center justify-center text-center gap-2.5 cursor-pointer transition outline-none"
                         :class="isDragging
                             ? 'border-amber-500 bg-amber-50/70 dark:bg-amber-950/25'
                             : (uploadNotice
                                 ? 'border-red-400 bg-red-50/40 dark:bg-red-950/20 focus:ring-2 focus:ring-red-400'
                                 : 'border-gray-200 dark:border-gray-700 bg-gray-50/80 dark:bg-gray-900/60 hover:border-amber-400 dark:hover:border-amber-500 hover:bg-amber-50/30 dark:hover:bg-amber-950/20 focus:ring-2 focus:ring-amber-500')">
                        <input type="file"
                               name="foto_files[]"
                               x-ref="fieldFoto"
                               multiple
                               accept="image/png, image/jpeg, image/webp"
                               @change="filesChosen"
                               class="sr-only">
                        <p class="text-xs sm:text-sm font-bold text-gray-800 dark:text-gray-200">
                            <span class="text-amber-600 dark:text-amber-400 underline underline-offset-4 decoration-amber-400">Pilih foto tambahan dari perangkat</span>
                            <span class="text-gray-600 dark:text-gray-300 font-medium"> atau seret dan lepas di sini</span>
                        </p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 font-medium max-w-md leading-relaxed">
                            Bisa memilih beberapa foto sekaligus atau bertahap. Format JPG, PNG, WEBP maksimal 20 MB per file.
                        </p>
                    </div>

                    <p x-show="uploadNotice" x-text="uploadNotice" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>

                    <!-- Preview Foto Tambahan Baru -->
                    <template x-if="selectedFiles.length > 0">
                        <div class="space-y-2 pt-1">
                            <span class="text-xs font-bold text-gray-700 dark:text-gray-300 block" x-text="newFilesCount + ' foto baru akan ditambahkan:'"></span>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                                <template x-for="(item, idx) in selectedFiles" :key="item.key">
                                    <div class="rounded-xl overflow-hidden bg-white dark:bg-gray-800 border border-amber-300 dark:border-amber-700 shadow-2xs flex flex-col">
                                        <div @click="openNewLightbox(idx)"
                                             class="relative aspect-[4/3] w-full overflow-hidden bg-gray-100 dark:bg-gray-900 cursor-pointer group">
                                            <img :src="item.url" :alt="item.name" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                            <div class="absolute inset-0 bg-black/45 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                                <span class="text-xs font-bold text-white tracking-wide">Pratinjau</span>
                                            </div>
                                        </div>
                                        <div class="px-3 py-2 flex items-center justify-between gap-2 bg-white dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700/80">
                                            <span class="text-[11px] font-bold text-gray-700 dark:text-gray-300 truncate"
                                                  x-text="'Foto ' + (existingCount + idx + 1) + ' • ' + item.sizeText"></span>
                                            <button type="button"
                                                    @click.stop="removeFile(idx)"
                                                    class="text-[11px] font-bold text-rose-600 dark:text-rose-400 hover:underline shrink-0 cursor-pointer">
                                                Hapus
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
                @error('foto_files')
                    <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                @enderror
                @error('foto_files.*')
                    <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action Button Footer (Rule #74: Grouping Rata Kanan, Batal di Samping Kiri Perbaharui Data, Rule #41: No border-t Divider) -->
            <div class="flex items-center justify-end gap-3 pt-4">
                <a href="{{ route('photographer.portfolio') }}" class="px-5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold text-xs transition text-center shadow-2xs cursor-pointer">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs shadow-xs transition cursor-pointer">
                    Perbaharui Data
                </button>
            </div>

        </form>

        <!-- Lightbox Pratinjau Foto (Rule #97) -->
        <div x-show="lightboxOpen"
             x-cloak
             @keydown.escape.window="lightboxOpen = false"
             @click.self="lightboxOpen = false"
             class="fixed inset-0 z-[9999] bg-black/90 backdrop-blur-md flex flex-col items-center justify-center p-4 sm:p-8">
            <button type="button"
                    @click="lightboxOpen = false"
                    aria-label="Tutup pratinjau"
                    class="absolute top-4 right-4 sm:top-6 sm:right-6 w-11 h-11 rounded-full bg-white/10 hover:bg-white/25 text-white flex items-center justify-center transition cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <img :src="lightboxUrl" :alt="lightboxCaption" class="max-w-full max-h-[80vh] object-contain rounded-2xl shadow-2xl border border-white/10 select-none">
            <div class="mt-4 px-4 py-2 rounded-full bg-white/10 text-xs font-semibold text-white/90" x-text="lightboxCaption"></div>
        </div>

    </div>

</div>

<script>
function portfolioEditApp() {
    return {
        existingCount: {{ $portfolio->medias->count() }},
        newFilesCount: 0,
        selectedFiles: [],
        uploadNotice: '',
        isDragging: false,
        catOpen: false,
        lightboxOpen: false,
        lightboxUrl: '',
        lightboxCaption: '',
        form: {
            judul: @js(old('judul', $portfolio->judul)),
            id_kategori: @js(old('id_kategori', $portfolio->id_kategori ?? '')),
            selectedCatName: @js($selectedCatName ?? '-- Pilih Kategori --'),
            deskripsi: @js(old('deskripsi', $portfolio->deskripsi))
        },
        touched: {
            judul: {{ $errors->has('judul') ? 'true' : 'false' }},
            id_kategori: {{ $errors->has('id_kategori') ? 'true' : 'false' }},
            deskripsi: {{ $errors->has('deskripsi') ? 'true' : 'false' }}
        },
        errors: {
            judul: '{{ $errors->first('judul') ?: '' }}',
            id_kategori: '{{ $errors->first('id_kategori') ?: '' }}',
            deskripsi: '{{ $errors->first('deskripsi') ?: '' }}'
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
        },
        syncInputFiles() {
            if (typeof DataTransfer !== 'undefined' && this.$refs.fieldFoto) {
                const dt = new DataTransfer();
                this.selectedFiles.forEach(item => dt.items.add(item.file));
                this.$refs.fieldFoto.files = dt.files;
            }
            this.newFilesCount = this.selectedFiles.length;
        },
        removeFile(idx) {
            const item = this.selectedFiles[idx];
            if (item && item.url) {
                URL.revokeObjectURL(item.url);
            }
            this.selectedFiles.splice(idx, 1);
            this.syncInputFiles();
            this.uploadNotice = '';
        },
        openDirectLightbox(url, caption) {
            this.lightboxUrl = url;
            this.lightboxCaption = caption || 'Pratinjau Foto';
            this.lightboxOpen = true;
        },
        openNewLightbox(idx) {
            const item = this.selectedFiles[idx];
            if (!item) return;
            this.lightboxUrl = item.url;
            this.lightboxCaption = `${item.name} • Foto Tambahan ${idx + 1}`;
            this.lightboxOpen = true;
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
        submitForm(e) {
            this.touched.judul = true;
            this.touched.id_kategori = true;
            this.touched.deskripsi = true;

            this.validateJudul();
            this.validateCategory();
            this.validateDeskripsi();

            let firstInvalidRef = null;
            if (this.errors.judul) firstInvalidRef = firstInvalidRef || this.$refs.fieldJudul;
            if (this.errors.id_kategori) firstInvalidRef = firstInvalidRef || this.$refs.fieldKategori;
            if (this.errors.deskripsi) firstInvalidRef = firstInvalidRef || this.$refs.fieldDeskripsi;

            if (this.errors.judul || this.errors.id_kategori || this.errors.deskripsi) {
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
