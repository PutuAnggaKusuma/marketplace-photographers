<div
  x-show="isProfileAddressModal"
  class="fixed inset-0 flex items-center justify-center p-5 overflow-y-auto z-99999"
>
  <div
    class="modal-close-btn fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]"
  ></div>
  <div
    @click.outside="isProfileAddressModal = false"
    class="no-scrollbar relative flex w-full max-w-[700px] flex-col overflow-y-auto rounded-3xl bg-white p-6 dark:bg-gray-900 lg:p-11"
  >
    <div class="px-2 pr-14">
      <h4 class="mb-2 text-2xl font-semibold text-gray-800 dark:text-white/90">
        Ubah Alamat
      </h4>
      <p class="mb-6 text-sm text-gray-500 dark:text-gray-400 lg:mb-7">
        Perbarui rincian lokasi domisili Anda.
      </p>
    </div>
    <form class="flex flex-col">
      <div class="px-2 overflow-y-auto custom-scrollbar">
        <div class="grid grid-cols-1 gap-x-6 gap-y-5 lg:grid-cols-2">
          <div>
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Negara
            </label>
            <input
              type="text"
              value=""
              placeholder="Indonesia"
              class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>

          <div>
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Kota / Wilayah
            </label>
            <input
              type="text"
              value=""
              placeholder="Masukkan kota atau wilayah"
              class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>

          <div>
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Kode Pos
            </label>
            <input
              type="text"
              value=""
              placeholder="Masukkan kode pos"
              class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>

          <div>
            <label
              class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
            >
              Nomor NPWP (Opsional)
            </label>
            <input
              type="text"
              value=""
              placeholder="Masukkan nomor NPWP jika ada"
              class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
            />
          </div>
        </div>
      </div>
      <div class="flex items-center justify-center gap-3 mt-6">
        <button
          @click="isProfileAddressModal = false"
          type="button"
          class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] sm:w-auto"
        >
          Tutup
        </button>
        <button
          type="button"
          class="flex w-full justify-center rounded-lg bg-amber-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-amber-600 sm:w-auto"
        >
          Simpan Perubahan
        </button>
      </div>
    </form>
  </div>
</div>
