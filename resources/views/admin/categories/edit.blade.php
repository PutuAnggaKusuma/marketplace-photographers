@extends('layouts.admin')



@section('title', 'Edit Kategori Layanan — Admin')



@section('content')

<div class="space-y-6">



    <!-- Standard Golden Header -->

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

        <div>
            <!-- Breadcrumb Navigation -->
            <nav class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 mb-1.5">
                <a href="{{ route('admin.categories') }}" class="hover:text-amber-500 transition">Kategori</a>
                <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                <span class="text-gray-900 dark:text-white font-bold">Edit Kategori</span>
            </nav>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">
                Edit Kategori Layanan
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

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm p-6 sm:p-8"
         x-data="categoryFormApp()">

        <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" @submit="submitForm($event)" novalidate class="space-y-5">

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
                       placeholder="Contoh: Wedding & Pernikahan, Prewedding Outdoor, Commercial Product" 
                       class="w-full px-4 py-3 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition"
                       :class="touched.nama_kategori && errors.nama_kategori ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500 focus:border-amber-500'">
                <p x-show="touched.nama_kategori && errors.nama_kategori" x-text="errors.nama_kategori" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>

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
                          rows="5" 
                          placeholder="Jelaskan cakupan layanan, target konsumen, dan kriteria foto pada kategori ini..." 
                          class="w-full min-h-[130px] px-4 py-3 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition resize-y leading-relaxed"
                          :class="touched.deskripsi && errors.deskripsi ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500 focus:border-amber-500'">{{ old('deskripsi', $category->deskripsi) }}</textarea>
                <p x-show="touched.deskripsi && errors.deskripsi" x-text="errors.deskripsi" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>

            </div>



            <!-- Action Button Footer (Rule #74 Standard: Batal di samping kiri tombol submit, rata kanan gap-3, Rule #41: No border-t Divider) -->
            <div class="flex items-center justify-end gap-3 pt-4">
                <a href="{{ route('admin.categories') }}" class="px-5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold text-xs transition text-center shadow-2xs cursor-pointer">
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
function categoryFormApp() {
    return {
        form: {
            nama_kategori: @js(old('nama_kategori', $category->nama_kategori)),
            deskripsi: @js(old('deskripsi', $category->deskripsi))
        },
        touched: {
            nama_kategori: {{ $errors->has('nama_kategori') ? 'true' : 'false' }},
            deskripsi: {{ $errors->has('deskripsi') ? 'true' : 'false' }}
        },
        errors: {
            nama_kategori: '{{ $errors->first('nama_kategori') ?: '' }}',
            deskripsi: '{{ $errors->first('deskripsi') ?: '' }}'
        },
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