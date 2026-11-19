@extends('layouts.admin')

@section('title', 'Tambah Kategori Layanan — Admin')

@section('content')
<div class="space-y-6" x-data="categoryFormApp()">

    <!-- Standard Golden Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <!-- Breadcrumb Navigation -->
            <nav class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 mb-1.5">
                <a href="{{ route('admin.categories') }}" class="hover:text-amber-500 transition">Kategori</a>
                <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                <span class="text-gray-900 dark:text-white font-bold">Tambah Kategori</span>
            </nav>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">
                Tambah Kategori Layanan Baru
            </h1>
        </div>

        <div>
            <a href="{{ route('admin.categories') }}" class="px-4 py-2 rounded-xl bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-750 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 font-bold text-xs transition inline-flex items-center gap-1.5 shadow-2xs shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"></path></svg>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <!-- Main Vertical Form Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm p-6 sm:p-8">
        <form action="{{ route('admin.categories.store') }}" 
              method="POST" 
              enctype="multipart/form-data" 
              @submit="submitForm($event)" 
              novalidate 
              class="space-y-6">
            @csrf

            <!-- 1. Nama Kategori -->
            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                    Nama Kategori Layanan <span class="text-rose-500">*</span>
                </label>
                <input type="text" 
                       name="nama_kategori" 
                       x-ref="fieldNama"
                       x-model="form.nama_kategori"
                       @input="touched.nama_kategori = true; validateNama()"
                       @blur="touched.nama_kategori = true; validateNama()"
                       placeholder="Contoh: Wedding, Wisuda, Produk Komersial" 
                       class="w-full px-4 py-3 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition"
                       :class="touched.nama_kategori && errors.nama_kategori ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500 focus:border-amber-500'">
                <p x-show="touched.nama_kategori && errors.nama_kategori" x-text="errors.nama_kategori" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
                @error('nama_kategori')
                    <p class="text-[11px] font-bold text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- 2. Deskripsi Kategori -->
            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">
                    Deskripsi Kategori Layanan <span class="text-rose-500">*</span>
                </label>
                <textarea name="deskripsi" 
                          x-ref="fieldDeskripsi"
                          x-model="form.deskripsi"
                          @input="touched.deskripsi = true; validateDeskripsi()"
                          @blur="touched.deskripsi = true; validateDeskripsi()"
                          rows="4" 
                          placeholder="Jelaskan cakupan layanan, target konsumen, dan kriteria foto pada kategori ini..." 
                          class="w-full min-h-[110px] px-4 py-3 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition resize-y leading-relaxed"
                          :class="touched.deskripsi && errors.deskripsi ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500 focus:border-amber-500'">{{ old('deskripsi') }}</textarea>
                <p x-show="touched.deskripsi && errors.deskripsi" x-text="errors.deskripsi" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
                @error('deskripsi')
                    <p class="text-[11px] font-bold text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- 3. Unggah Foto Kategori (Dropzone & Lightbox - Rule #97 Compliant) -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">
                    Foto Sampul Kategori <span class="text-gray-400 font-normal">(Opsional)</span>
                </label>

                <div class="space-y-4">
                    <!-- Dropzone Container -->
                    <div tabindex="0"
                         role="button"
                         @click="$refs.fileInput.click()"
                         @keydown.enter.prevent="$refs.fileInput.click()"
                         @keydown.space.prevent="$refs.fileInput.click()"
                         @dragover.prevent="isDragging = true"
                         @dragleave.prevent="isDragging = false"
                         @drop.prevent="handleDrop($event)"
                         class="w-full min-h-[140px] px-6 py-7 rounded-2xl border-2 border-dashed flex flex-col items-center justify-center text-center gap-2 cursor-pointer transition outline-none"
                         :class="isDragging
                             ? 'border-amber-500 bg-amber-50/70 dark:bg-amber-950/25'
                             : (fileError
                                 ? 'border-red-400 bg-red-50/40 dark:bg-red-950/20 focus:ring-2 focus:ring-red-400'
                                 : 'border-gray-200 dark:border-gray-700 bg-gray-50/80 dark:bg-gray-900/60 hover:border-amber-400 dark:hover:border-amber-500 hover:bg-amber-50/30 dark:hover:bg-amber-950/20 focus:ring-2 focus:ring-amber-500')">
                        <input type="file" 
                               name="foto_kategori" 
                               x-ref="fileInput" 
                               accept="image/jpeg,image/png,image/webp" 
                               @change="handleFileChosen($event)" 
                               class="sr-only">
                        <svg class="w-7 h-7 sm:w-8 sm:h-8 transition-colors" :class="isDragging ? 'text-amber-500' : 'text-gray-400 dark:text-gray-500'" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                        </svg>
                        <p class="text-xs sm:text-sm font-bold text-gray-700 dark:text-gray-200">
                            Pilih atau seret foto kategori ke sini
                        </p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 font-medium">
                            Format JPG, PNG, WEBP maksimal 10 MB.
                        </p>
                    </div>

                    <!-- Pratinjau Foto Terpilih -->
                    <template x-if="previewUrl">
                        <div class="rounded-xl overflow-hidden bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 max-w-sm shadow-2xs">
                            <div @click="lightboxOpen = true" class="relative aspect-[16/9] w-full overflow-hidden bg-gray-100 dark:bg-gray-900 cursor-pointer group">
                                <img :src="previewUrl" alt="Pratinjau Foto Kategori" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                <div class="absolute inset-0 bg-black/45 opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                    <span class="text-xs font-bold text-white tracking-wide">Pratinjau Foto</span>
                                </div>
                            </div>
                            <div class="px-3.5 py-2.5 flex items-center justify-between gap-2 border-t border-gray-100 dark:border-gray-700/80">
                                <span class="text-[11px] font-bold text-gray-700 dark:text-gray-300 truncate" x-text="fileName || 'Foto Kategori'"></span>
                                <button type="button" @click="removePhoto()" class="text-[11px] font-bold text-rose-600 dark:text-rose-400 hover:underline shrink-0 cursor-pointer">
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <p x-show="fileError" x-text="fileError" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>
                @error('foto_kategori')
                    <p class="text-[11px] font-bold text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action Button Footer (Rule #74 Standard: Batal di samping kiri tombol submit, rata kanan gap-3, Rule #41: No border-t Divider) -->
            <div class="flex items-center justify-end gap-3 pt-3">
                <a href="{{ route('admin.categories') }}" class="px-5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold text-xs transition text-center shadow-2xs cursor-pointer">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs shadow-xs transition cursor-pointer">
                    Simpan Kategori
                </button>
            </div>
        </form>
    </div>

    <!-- Lightbox Pratinjau Resolusi Penuh (Rule #97 Compliant) -->
    <template x-teleport="body">
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
            <img :src="previewUrl" :alt="fileName" class="max-w-full max-h-[82vh] object-contain rounded-2xl shadow-2xl border border-white/10 select-none">
        </div>
    </template>
</div>

<script>
function categoryFormApp() {
    return {
        form: {
            nama_kategori: @js(old('nama_kategori', '')),
            deskripsi: @js(old('deskripsi', ''))
        },
        touched: {
            nama_kategori: {{ $errors->has('nama_kategori') ? 'true' : 'false' }},
            deskripsi: {{ $errors->has('deskripsi') ? 'true' : 'false' }}
        },
        errors: {
            nama_kategori: '{{ $errors->first('nama_kategori') ?: '' }}',
            deskripsi: '{{ $errors->first('deskripsi') ?: '' }}'
        },
        isDragging: false,
        previewUrl: null,
        fileName: '',
        fileError: '',
        lightboxOpen: false,

        validateNama() {
            if (!this.form.nama_kategori || this.form.nama_kategori.trim() === '') {
                this.errors.nama_kategori = 'Nama kategori layanan wajib diisi.';
            } else if (this.form.nama_kategori.trim().length < 3) {
                this.errors.nama_kategori = 'Nama kategori minimal 3 karakter.';
            } else {
                this.errors.nama_kategori = '';
            }
        },
        validateDeskripsi() {
            if (!this.form.deskripsi || this.form.deskripsi.trim() === '') {
                this.errors.deskripsi = 'Deskripsi kategori layanan wajib diisi.';
            } else if (this.form.deskripsi.trim().length < 10) {
                this.errors.deskripsi = 'Deskripsi kategori minimal 10 karakter.';
            } else {
                this.errors.deskripsi = '';
            }
        },
        handleFileChosen(e) {
            const files = e.target.files;
            if (files && files[0]) {
                this.processFile(files[0]);
            }
        },
        handleDrop(e) {
            this.isDragging = false;
            const files = e.dataTransfer.files;
            if (files && files[0]) {
                this.$refs.fileInput.files = files;
                this.processFile(files[0]);
            }
        },
        processFile(file) {
            this.fileError = '';
            const validTypes = ['image/jpeg', 'image/png', 'image/webp'];
            if (!validTypes.includes(file.type)) {
                this.fileError = 'Format berkas harus JPG, PNG, atau WEBP.';
                this.removePhoto();
                return;
            }
            if (file.size > 10 * 1024 * 1024) {
                this.fileError = 'Ukuran berkas maksimal 10 MB.';
                this.removePhoto();
                return;
            }
            this.fileName = file.name;
            const reader = new FileReader();
            reader.onload = (e) => {
                this.previewUrl = e.target.result;
            };
            reader.readAsDataURL(file);
        },
        removePhoto() {
            this.previewUrl = null;
            this.fileName = '';
            this.fileError = '';
            if (this.$refs.fileInput) {
                this.$refs.fileInput.value = '';
            }
        },
        submitForm(e) {
            this.touched.nama_kategori = true;
            this.touched.deskripsi = true;

            this.validateNama();
            this.validateDeskripsi();

            let firstInvalidRef = null;
            if (this.errors.nama_kategori) firstInvalidRef = firstInvalidRef || this.$refs.fieldNama;
            if (this.errors.deskripsi) firstInvalidRef = firstInvalidRef || this.$refs.fieldDeskripsi;

            if (this.errors.nama_kategori || this.errors.deskripsi) {
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
