@extends('layouts.admin')



@section('title', 'Edit Materi E-Learning — Admin')



@section('content')

<div class="space-y-6">



    <!-- Standard Golden Header -->

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

        <div>
            <!-- Breadcrumb Navigation -->
            <nav class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 mb-1.5">
                <a href="{{ route('admin.elearning') }}" class="hover:text-amber-500 transition">E-Learning</a>
                <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                <span class="text-gray-900 dark:text-white font-bold">Edit Materi</span>
            </nav>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">
                Edit Materi E-Learning
            </h1>
        </div>

        <div>

            <a href="{{ route('admin.elearning') }}" class="px-4 py-2 rounded-xl bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-750 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 font-bold text-xs transition inline-flex items-center gap-1.5 shadow-2xs shrink-0">

                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"></path></svg>

                <span>Kembali</span>

            </a>

        </div>

    </div>



    <!-- Main Vertical Form Card -->

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm p-6 sm:p-8"
         x-data="elearningEditApp()">

        <form action="{{ route('admin.elearning.update', $material->id) }}" method="POST" enctype="multipart/form-data" @submit="submitForm($event)" novalidate class="space-y-5">

            @csrf



            <!-- Row 1: Judul Materi & Kategori (Horizontal 2 Kolom) -->

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div>

                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">

                    Judul Materi E-Learning <span class="text-rose-500">*</span>

                </label>

                <input type="text" 
                       name="judul" 
                       x-ref="fieldJudul"
                       x-model="form.judul"
                       @input="touched.judul = true; validateJudul()"
                       @blur="touched.judul = true; validateJudul()"
                       placeholder="Contoh: Menguasai Komposisi Golden Ratio dalam Fotografi Portrait Outdoor" 
                       class="w-full px-4 py-3 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition"
                       :class="touched.judul && errors.judul ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500 focus:border-amber-500'">
                <p x-show="touched.judul && errors.judul" x-text="errors.judul" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>

            </div>



            <div>

                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">

                    Kategori Materi <span class="text-rose-500">*</span>

                </label>

                @php

                    $selectedCatId = old('id_kategori', $material->id_kategori ?? '');

                    $selectedCatName = '';

                    foreach($categories as $cat) {

                        if ($cat->id == $selectedCatId) {

                            $selectedCatName = $cat->nama_kategori;

                            break;

                        }

                    }

                    if (!$selectedCatName) {

                        $selectedCatName = '-- Pilih Kategori Materi --';

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



            <!-- 3. Tipe Akses & Harga Kursus (Pure Vertical Dropdown) -->

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Dropdown Tipe Akses -->

                <div>

                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">

                        Tipe Akses Kursus <span class="text-rose-500">*</span>

                    </label>

                    <div class="relative w-full">

                        <input type="hidden" name="is_premium" :value="form.isPremium">

                        <button @click="openAccess = !openAccess" type="button" 

                                class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs font-bold text-gray-900 dark:text-white flex items-center justify-between gap-2 shadow-2xs hover:border-amber-400 dark:hover:border-amber-500 cursor-pointer transition">

                            <span x-text="form.accessName" class="truncate"></span>

                            <svg class="w-4 h-4 text-gray-400 shrink-0 transition-transform duration-200" :class="openAccess ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>

                        </button>

                        <div x-show="openAccess" @click.away="openAccess = false" x-transition 

                             class="absolute z-50 left-0 mt-2 w-full bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700/80 py-1 overflow-hidden" style="display: none;">

                            <button type="button" 

                                    @click="selectAccess('0', 'Akses Gratis (Semua Pengguna)')" 

                                    class="w-full px-4 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition flex items-center justify-between text-left cursor-pointer border-b border-gray-50 dark:border-gray-700/40"

                                    :class="form.isPremium === '0' ? 'bg-amber-50/70 dark:bg-amber-950/30 text-amber-600 font-extrabold' : ''">

                                <span>Akses Gratis (Semua Pengguna)</span>

                                <span x-show="form.isPremium === '0'" class="shrink-0"><svg class="w-4 h-4 text-amber-600 dark:text-amber-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>

                            </button>

                            <button type="button" 

                                    @click="selectAccess('1', 'Berbayar (Akses Kursus Berbayar)')" 

                                    class="w-full px-4 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition flex items-center justify-between text-left cursor-pointer"

                                    :class="form.isPremium === '1' ? 'bg-amber-50/70 dark:bg-amber-950/30 text-amber-600 font-extrabold' : ''">

                                <span>Berbayar (Akses Kursus Berbayar)</span>

                                <span x-show="form.isPremium === '1'" class="shrink-0"><svg class="w-4 h-4 text-amber-600 dark:text-amber-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>

                            </button>

                        </div>

                    </div>

                </div>



                <!-- Harga Kursus Vertikal di Bawahnya (Hanya Muncul Jika Berbayar) -->

                <div x-show="form.isPremium === '1'" x-transition class="space-y-1.5">

                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">

                        Harga Kursus (Rp) <span class="text-rose-500">*</span>

                    </label>

                    <input type="number" 
                           name="harga" 
                           x-ref="fieldHarga"
                           x-model="form.harga"
                           @input="touched.harga = true; validateHarga()"
                           @blur="touched.harga = true; validateHarga()"
                           min="0"
                           step="1000"
                           placeholder="Masukkan nominal harga kursus" 
                           class="w-full px-4 py-3 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition"
                           :class="touched.harga && errors.harga ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500 focus:border-amber-500'">

                    <p class="text-[11px] text-gray-400 dark:text-gray-500">Masukkan nominal angka murni tanpa titik atau koma.</p>

                    <p x-show="touched.harga && errors.harga" x-text="errors.harga" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>

                </div>

            </div>



            <!-- 4. URL Video Youtube (Opsional) -->

            <div>

                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">

                    Link Video Pembelajaran YouTube / Vimeo (Opsional)

                </label>

                <input type="url" 

                       name="video_url" 

                       x-model="form.video_url"

                       placeholder="https://www.youtube.com/watch?v=..." 

                       class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition @error('video_url') border-rose-500 @enderror">

                @error('video_url')

                    <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>

                @enderror

            </div>



            <!-- 4. Konten / Isi Pembelajaran -->

            <div>

                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">

                    Isi Lengkap Modul Pembelajaran <span class="text-rose-500">*</span>

                </label>

                <textarea name="konten" 
                          x-ref="fieldKonten"
                          x-model="form.konten"
                          @input="touched.konten = true; validateKonten()"
                          @blur="touched.konten = true; validateKonten()"
                          rows="8" 
                          placeholder="Tuliskan materi pembelajaran, rincian teknis, langkah-langkah praktis, dan panduan foto selengkapnya..." 
                          class="w-full px-4 py-3 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition resize-y leading-relaxed"
                          :class="touched.konten && errors.konten ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500 focus:border-amber-500'">{{ old('konten', $material->konten) }}</textarea>

                <p x-show="touched.konten && errors.konten" x-text="errors.konten" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>

            </div>



            <!-- 5. Foto Thumbnail Modul -->

            <div>

                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">

                    Cover Banner / Gambar Thumbnail Modul

                </label>

                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">

                    <div class="w-48 h-28 rounded-2xl bg-gray-100 dark:bg-gray-700 border flex items-center justify-center overflow-hidden shrink-0 shadow-xs transition"
                         :class="thumbnailError ? 'border-red-400' : 'border-gray-200 dark:border-gray-600'">

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



                    <div class="space-y-2">

                        <label class="cursor-pointer inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs shadow-xs transition">

                            <span>Ganti Thumbnail</span>

                            <input type="file" name="thumbnail" accept="image/png, image/jpeg, image/webp" @change="fileChosen" class="hidden">

                        </label>

                        <p class="text-[11px] text-gray-400 dark:text-gray-500 font-medium">Format: JPG, PNG, WEBP. Maksimal 10 MB.</p>

                    </div>

                </div>

                <p x-show="thumbnailError" x-text="thumbnailError" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>

                @error('thumbnail')

                    <p class="text-[11px] text-rose-500 font-bold mt-1">{{ $message }}</p>

                @enderror

            </div>



            <!-- Action Button Footer (Rule #74 Standard: Batal di samping kiri tombol submit, rata kanan gap-3) -->

            <!-- Action Button Footer (Rule #74 Standard: Batal di samping kiri tombol submit, rata kanan gap-3, Rule #41: No border-t Divider) -->
            <div class="flex items-center justify-end gap-3 pt-4">
                <a href="{{ route('admin.elearning') }}" class="px-5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold text-xs transition text-center shadow-2xs cursor-pointer">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs shadow-xs transition cursor-pointer">
                    Perbaharui Data
                </button>
            </div>

        </form>

    </div>

</div>

<script>
function elearningEditApp() {
    return {
        previewUrl: @js($material->thumbnail_url ?: ($material->thumbnail ? asset('storage/' . $material->thumbnail) : '')),
        thumbnailError: '',
        fileChosen(event) {
            const res = window.validateUploadFiles(event.target, { maxMB: 10 });
            if (res.hasErrors) {
                this.thumbnailError = res.errorMessage;
                return;
            }
            const file = res.validFiles[0];
            if (file) {
                this.thumbnailError = '';
                this.previewUrl = URL.createObjectURL(file);
            }
        },
        catOpen: false,
        openAccess: false,
        form: {
            judul: @js(old('judul', $material->judul)),
            id_kategori: @js(old('id_kategori', $material->id_kategori ?? '')),
            selectedCatName: @js($selectedCatName ?? '-- Pilih Kategori Materi --'),
            isPremium: @js(old('is_premium', $material->is_premium ? '1' : '0')),
            accessName: @js(old('is_premium', $material->is_premium ? '1' : '0') == '1' ? 'Berbayar (Akses Kursus Berbayar)' : 'Akses Gratis (Semua Pengguna)'),
            harga: @js(old('harga', $material->harga)),
            video_url: @js(old('video_url', $material->video_url)),
            konten: @js(old('konten', $material->konten))
        },
        touched: {
            judul: {{ $errors->has('judul') ? 'true' : 'false' }},
            id_kategori: {{ $errors->has('id_kategori') ? 'true' : 'false' }},
            harga: {{ $errors->has('harga') ? 'true' : 'false' }},
            konten: {{ $errors->has('konten') ? 'true' : 'false' }}
        },
        errors: {
            judul: '{{ $errors->first('judul') ?: '' }}',
            id_kategori: '{{ $errors->first('id_kategori') ?: '' }}',
            harga: '{{ $errors->first('harga') ?: '' }}',
            konten: '{{ $errors->first('konten') ?: '' }}'
        },
        selectCategory(id, name) {
            this.form.id_kategori = id;
            this.form.selectedCatName = name;
            this.catOpen = false;
            this.touched.id_kategori = true;
            this.validateCategory();
        },
        selectAccess(premium, name) {
            this.form.isPremium = premium;
            this.form.accessName = name;
            this.openAccess = false;
            if (premium === '0') {
                this.errors.harga = '';
            } else if (this.touched.harga) {
                this.validateHarga();
            }
        },
        validateJudul() {
            if (!this.form.judul || this.form.judul.trim() === '') {
                this.errors.judul = 'Judul materi e-learning wajib diisi.';
            } else if (this.form.judul.trim().length < 5) {
                this.errors.judul = 'Judul materi minimal 5 karakter.';
            } else {
                this.errors.judul = '';
            }
        },
        validateCategory() {
            if (!this.form.id_kategori) {
                this.errors.id_kategori = 'Kategori materi wajib dipilih.';
            } else {
                this.errors.id_kategori = '';
            }
        },
        validateHarga() {
            if (this.form.isPremium === '1') {
                if (!this.form.harga || Number(this.form.harga) < 1000) {
                    this.errors.harga = 'Harga kursus berbayar minimal Rp 1.000.';
                } else {
                    this.errors.harga = '';
                }
            } else {
                this.errors.harga = '';
            }
        },
        validateKonten() {
            if (!this.form.konten || this.form.konten.trim() === '') {
                this.errors.konten = 'Isi modul pembelajaran wajib diisi.';
            } else if (this.form.konten.trim().length < 20) {
                this.errors.konten = 'Isi modul pembelajaran minimal 20 karakter.';
            } else {
                this.errors.konten = '';
            }
        },
        submitForm(e) {
            this.touched.judul = true;
            this.touched.id_kategori = true;
            this.touched.konten = true;
            if (this.form.isPremium === '1') {
                this.touched.harga = true;
                this.validateHarga();
            }

            this.validateJudul();
            this.validateCategory();
            this.validateKonten();

            let firstInvalidRef = null;
            if (this.errors.judul) firstInvalidRef = firstInvalidRef || this.$refs.fieldJudul;
            if (this.errors.id_kategori) firstInvalidRef = firstInvalidRef || this.$refs.fieldKategori;
            if (this.form.isPremium === '1' && this.errors.harga) firstInvalidRef = firstInvalidRef || this.$refs.fieldHarga;
            if (this.errors.konten) firstInvalidRef = firstInvalidRef || this.$refs.fieldKonten;

            if (this.errors.judul || this.errors.id_kategori || (this.form.isPremium === '1' && this.errors.harga) || this.errors.konten) {
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