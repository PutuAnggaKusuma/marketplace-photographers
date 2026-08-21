<!-- Universal Validation & Confirmation Modal Dialog -->
<div 
  x-data="{
    confirmOpen: false,
    modalTitle: 'Konfirmasi Aksi',
    modalMessage: 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
    modalActionUrl: '',
    modalMethod: 'POST',
    modalButtonText: 'Ya, Lanjutkan',
    modalButtonClass: 'bg-red-600 hover:bg-red-700 text-white',
    iconType: 'warning',
    openModal(data) {
      this.modalTitle = data.title || 'Konfirmasi Aksi';
      this.modalMessage = data.message || 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
      this.modalActionUrl = data.actionUrl || '';
      this.modalMethod = data.method || 'POST';
      this.modalButtonText = data.buttonText || 'Ya, Lanjutkan';
      this.modalButtonClass = data.buttonClass || 'bg-red-600 hover:bg-red-700 text-white';
      this.iconType = data.iconType || 'warning';
      this.confirmOpen = true;
    }
  }"
  @open-confirm-modal.window="openModal($event.detail)"
  x-show="confirmOpen"
  x-cloak
  @keydown.escape.window="confirmOpen = false"
  class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
  x-transition
>
  <div @click.away="confirmOpen = false" class="bg-white dark:bg-gray-800 max-w-md w-full p-6 sm:p-7 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-2xl space-y-6 text-center">
    
    <!-- Icon Container -->
    <div class="w-14 h-14 rounded-full mx-auto flex items-center justify-center shrink-0 shadow-sm"
      :class="{
        'bg-red-100 dark:bg-red-950/60 text-red-600 dark:text-red-400': iconType === 'danger' || iconType === 'logout',
        'bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400': iconType === 'warning',
        'bg-blue-100 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400': iconType === 'info'
      }">
      
      <template x-if="iconType === 'logout'">
        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
      </template>
      <template x-if="iconType === 'danger'">
        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
      </template>
      <template x-if="iconType === 'warning'">
        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
      </template>
      <template x-if="iconType === 'info'">
        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
      </template>
    </div>

    <!-- Modal Title & Description -->
    <div class="space-y-2">
      <h3 class="text-lg font-black text-gray-900 dark:text-white" x-text="modalTitle"></h3>
      <p class="text-xs text-gray-500 leading-relaxed" x-text="modalMessage"></p>
    </div>

    <!-- Form & Action Buttons -->
    <form :action="modalActionUrl" :method="modalMethod === 'GET' ? 'GET' : 'POST'" class="space-y-3">
      @csrf
      <template x-if="modalMethod !== 'GET' && modalMethod !== 'POST'">
        <input type="hidden" name="_method" :value="modalMethod">
      </template>

      <div class="grid grid-cols-2 gap-3 pt-2">
        <button type="button" @click="confirmOpen = false" class="py-3 px-4 rounded-xl border border-gray-200 dark:border-gray-700 text-xs font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
          Batal
        </button>
        <button type="submit" :class="modalButtonClass" class="py-3 px-4 rounded-xl text-xs font-extrabold shadow-md transition">
          <span x-text="modalButtonText"></span>
        </button>
      </div>
    </form>

  </div>
</div>