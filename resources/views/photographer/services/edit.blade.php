@extends('layouts.photographer')



@section('title', 'Edit Paket Layanan — Fotografer')



@section('content')

<div class="space-y-6">



    <!-- Standard Golden Header -->

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

        <div>
            <!-- Breadcrumb Navigation -->
            <nav class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 mb-1.5">
                <a href="{{ route('photographer.services') }}" class="hover:text-amber-500 transition">Paket Layanan</a>
                <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                <span class="text-gray-900 dark:text-white font-bold">Edit Layanan</span>
            </nav>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">
                Edit Paket Layanan
            </h1>
        </div>

        <div>

            <a href="{{ route('photographer.services') }}" class="px-4 py-2 rounded-xl bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-750 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 font-bold text-xs transition inline-flex items-center gap-1.5 shadow-2xs shrink-0">

                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"></path></svg>

                <span>Kembali</span>

            </a>

        </div>

    </div>



    @php
        $existingFiturs = $service->details->pluck('nama_fitur')->toArray();
        $initialFiturs = old('fitur', !empty($existingFiturs) ? $existingFiturs : ['Durasi Sesi 3 Jam', '25 Foto Retouched & Full Editing']);
        if (!is_array($initialFiturs) || count($initialFiturs) === 0) {
            $initialFiturs = ['Durasi Sesi 3 Jam', '25 Foto Retouched & Full Editing'];
        } else {
            $initialFiturs = array_values($initialFiturs);
        }
        $fiturError = $errors->first('fitur') ?: ($errors->first('fitur.*') ?: '');
    @endphp

    <!-- Main Vertical Form Card -->

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm p-6 sm:p-8"

         x-data="serviceFormApp()">

        

        <form action="{{ route('photographer.services.update', $service->id) }}" method="POST" @submit="submitForm($event)" novalidate class="space-y-5">

            @csrf



            <!-- 1. Nama Paket Layanan & 2. Tarif Harga (Horizontal 2 Kolom) -->

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div>

                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">

                    Nama Paket Layanan <span class="text-rose-500">*</span>

                </label>

                <input type="text" 
                       name="nama_layanan" 
                       x-ref="fieldNama"
                       x-model="form.nama_layanan"
                       @input="touched.nama_layanan = true; validateNama()"
                       @blur="touched.nama_layanan = true; validateNama()"
                       maxlength="100" placeholder="Contoh: Paket Prewedding Romantic Sunset" 
                       class="w-full px-4 py-3 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition"
                       :class="touched.nama_layanan && errors.nama_layanan ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500 focus:border-amber-500'">
                <p x-show="touched.nama_layanan && errors.nama_layanan" x-text="errors.nama_layanan" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>

            </div>



            <div>

                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">

                    Tarif Harga (Rp) <span class="text-rose-500">*</span>

                </label>

                <input type="number" 
                       name="tarif_harga" 
                       x-ref="fieldTarif"
                       x-model="form.tarif_harga"
                       @input="touched.tarif_harga = true; validateTarif()"
                       @blur="touched.tarif_harga = true; validateTarif()"
                       step="1000"
                       placeholder="Masukkan nominal tarif paket (min. 10000)" 
                       class="w-full px-4 py-3 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition"
                       :class="touched.tarif_harga && errors.tarif_harga ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500 focus:border-amber-500'">
                <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-1">Masukkan nominal angka murni tanpa titik atau koma.</p>
                <p x-show="touched.tarif_harga && errors.tarif_harga" x-text="errors.tarif_harga" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>

            </div>



            </div>



            <!-- 3. Deskripsi Layanan -->

            <div>

                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1.5">

                    Deskripsi Lengkap Paket <span class="text-rose-500">*</span>

                </label>

                <textarea name="deskripsi_layanan" 
                          x-ref="fieldDeskripsi"
                          x-model="form.deskripsi_layanan"
                          @input="touched.deskripsi_layanan = true; validateDeskripsi()"
                          @blur="touched.deskripsi_layanan = true; validateDeskripsi()"
                          rows="6" 
                          maxlength="1000" placeholder="Tuliskan cakupan pemotretan, konsep lokasi, dan ketentuan umum sesi foto (minimal 15 karakter)..." 
                          class="w-full min-h-[150px] px-4 py-3 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition resize-y leading-relaxed"
                          :class="touched.deskripsi_layanan && errors.deskripsi_layanan ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500 focus:border-amber-500'"></textarea>
                <p x-show="touched.deskripsi_layanan && errors.deskripsi_layanan" x-text="errors.deskripsi_layanan" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>

            </div>



            <!-- 4. Rincian Fasilitas / Fitur Poin -->

            <div>

                <div class="flex flex-col items-start gap-1.5 mb-2.5">

                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300">

                        Rincian Benefit & Fasilitas yang Didapat Klien <span class="text-rose-500">*</span>

                    </label>

                    <button type="button" 

                            @click="addFitur()" 

                            class="text-xs font-extrabold text-amber-600 dark:text-amber-400 hover:text-amber-700 flex items-center gap-1 transition cursor-pointer">

                        <span>+ Tambah Benefit</span>

                    </button>

                </div>

                

                <div class="space-y-2">

                    <template x-for="(fitur, index) in fiturs" :key="index">

                        <div class="flex items-center gap-2">

                            <input type="text" 

                                   name="fitur[]" 

                                   x-model="fiturs[index]" 

                                   @input="touched.fitur = true; validateFitur()"
                                   @blur="touched.fitur = true; validateFitur()"

                                   placeholder="Contoh: 20 Foto Retouch & Full Color Grading" 

                                   class="flex-1 px-4 py-2.5 rounded-xl border bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 transition"
                                   :class="touched.fitur && errors.fitur ? 'border-red-400 focus:ring-red-400 focus:border-red-400' : 'border-gray-200 dark:border-gray-700 focus:ring-amber-500 focus:border-amber-500'">

                            <button type="button" 

                                    @click="removeFitur(index); touched.fitur = true; validateFitur()" 

                                    class="p-2.5 rounded-xl border border-rose-200 dark:border-rose-900/60 bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 hover:bg-rose-100 transition shrink-0 cursor-pointer" 

                                    title="Hapus Benefit">

                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>

                            </button>

                        </div>

                    </template>

                    <p x-show="touched.fitur && errors.fitur" x-text="errors.fitur" x-cloak class="text-[11px] font-bold text-red-500 mt-1"></p>

                </div>

            </div>



            <!-- Action Button Footer (Rule #74 Standard: Batal di samping kiri tombol aksi utama, rata kanan gap-3) -->

            <!-- Action Button Footer (Rule #74 Standard: Batal di samping kiri tombol submit, rata kanan gap-3, Rule #41: No border-t Divider) -->
            <div class="flex items-center justify-end gap-3 pt-4">

                <a href="{{ route('photographer.services') }}" class="px-5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold text-xs transition text-center shadow-2xs cursor-pointer">

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
function serviceFormApp() {
  return {
    fiturs: @js($initialFiturs),
    addFitur() { this.fiturs.push(''); },
    removeFitur(index) { if (this.fiturs.length > 1) this.fiturs.splice(index, 1); },
    form: {
      nama_layanan: @js(old('nama_layanan', $service->nama_layanan)),
      tarif_harga: @js(old('tarif_harga', (int)$service->tarif_harga)),
      deskripsi_layanan: @js(old('deskripsi_layanan', $service->deskripsi_layanan)),
    },
    touched: {
      nama_layanan: {{ $errors->has('nama_layanan') ? 'true' : 'false' }},
      tarif_harga: {{ $errors->has('tarif_harga') ? 'true' : 'false' }},
      deskripsi_layanan: {{ $errors->has('deskripsi_layanan') ? 'true' : 'false' }},
      fitur: {{ ($errors->has('fitur') || $errors->has('fitur.*')) ? 'true' : 'false' }},
    },
    errors: {
      nama_layanan: @js($errors->first('nama_layanan') ?: ''),
      tarif_harga: @js($errors->first('tarif_harga') ?: ''),
      deskripsi_layanan: @js($errors->first('deskripsi_layanan') ?: ''),
      fitur: @js($fiturError),
    },
    validateNama() {
      const val = (this.form.nama_layanan || '').trim();
      if (!val) this.errors.nama_layanan = 'Nama paket layanan wajib diisi.';
      else if (val.length < 3) this.errors.nama_layanan = 'Nama paket layanan minimal 3 karakter.';
      else if (val.length > 100) this.errors.nama_layanan = 'Nama paket layanan maksimal 100 karakter.';
      else this.errors.nama_layanan = '';
    },
    validateTarif() {
      const val = this.form.tarif_harga;
      if (!val && val !== 0 && val !== '0') this.errors.tarif_harga = 'Tarif harga paket wajib diisi.';
      else if (Number(val) < 10000) this.errors.tarif_harga = 'Tarif harga minimal Rp 10.000.';
      else this.errors.tarif_harga = '';
    },
    validateDeskripsi() {
      const val = (this.form.deskripsi_layanan || '').trim();
      if (!val) this.errors.deskripsi_layanan = 'Deskripsi paket layanan wajib diisi.';
      else if (val.length < 15) this.errors.deskripsi_layanan = 'Deskripsi paket layanan minimal 15 karakter.';
      else this.errors.deskripsi_layanan = '';
    },
    validateFitur() {
      if (!this.fiturs || this.fiturs.length === 0) {
        this.errors.fitur = 'Minimal 1 benefit layanan wajib diisi.';
        return false;
      }
      for (let i = 0; i < this.fiturs.length; i++) {
        const item = (this.fiturs[i] || '').trim();
        if (!item) {
          this.errors.fitur = 'Benefit layanan tidak boleh kosong.';
          return false;
        }
        if (item.length < 3) {
          this.errors.fitur = 'Benefit layanan minimal 3 karakter.';
          return false;
        }
      }
      this.errors.fitur = '';
      return true;
    },
    submitForm(e) {
      this.touched.nama_layanan = true;
      this.touched.tarif_harga = true;
      this.touched.deskripsi_layanan = true;
      this.touched.fitur = true;
      this.validateNama();
      this.validateTarif();
      this.validateDeskripsi();
      this.validateFitur();
      if (this.errors.nama_layanan || this.errors.tarif_harga || this.errors.deskripsi_layanan || this.errors.fitur) {
        e.preventDefault();
        let targetEl = null;
        if (this.errors.nama_layanan) targetEl = this.$refs.fieldNama;
        else if (this.errors.tarif_harga) targetEl = this.$refs.fieldTarif;
        else if (this.errors.deskripsi_layanan) targetEl = this.$refs.fieldDeskripsi;
        else if (this.errors.fitur) {
          const inputs = Array.from(this.$el.querySelectorAll('input[name="fitur[]"]'));
          targetEl = inputs.find(el => !(el.value || '').trim() || (el.value || '').trim().length < 3) || inputs[0];
        }
        if (targetEl) {
          targetEl.focus?.();
          targetEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        return false;
      }
    }
  };
}
</script>

@endsection