@php
  $__accessAuthUser = auth()->user();
  $__holdPopupPayload = $__accessAuthUser ? $__accessAuthUser->getPhotographerHoldPopupPayload() : null;
@endphp

<!-- Universal Role & Verification Access Notice Modal (Rule #6 & #59 Compliant) -->
<div id="access-notice-modal-root"
     x-data="{
       accessModalOpen: false,
       title: '',
       message: '',
       note: '',
       buttonText: 'Mengerti',
       redirectUrl: '',
       openModal(detail) {
         if (!detail) return;
         this.title = detail.title || 'Akses Terbatas';
         this.message = detail.message || '';
         this.note = detail.note || '';
         this.buttonText = detail.buttonText || 'Mengerti';
         this.redirectUrl = detail.redirectUrl || '';
         this.accessModalOpen = true;
       },
       closeModal() {
         this.accessModalOpen = false;
       }
     }"
     @open-access-modal.window="openModal($event.detail)"
     @keydown.escape.window="closeModal()"
     x-show="accessModalOpen"
     x-cloak
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 scale-95"
     x-transition:enter-end="opacity-100 scale-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 scale-100"
     x-transition:leave-end="opacity-0 scale-95"
     class="fixed inset-0 z-[100] overflow-y-auto"
     role="dialog"
     aria-modal="true">
  <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="closeModal()"></div>
  <div class="flex min-h-full items-center justify-center p-4">
    <div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 text-center shadow-xl transition-all sm:my-8 w-full max-w-[420px] border border-gray-200 dark:border-gray-700 p-7 sm:p-8 flex flex-col gap-4" @click.stop>

      <!-- Plain Close Button Absolute Top Right (SVG Vector per Rule #6 & #59) -->
      <button type="button" @click="closeModal()" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 dark:hover:text-white transition cursor-pointer p-1" aria-label="Tutup">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>

      <!-- Header without Icon & Centered -->
      <div class="text-center">
        <h3 class="text-base sm:text-lg font-black text-gray-900 dark:text-white" x-text="title"></h3>
      </div>

      <!-- Body Text (Centered) -->
      <div class="flex flex-col gap-2 text-center">
        <p class="text-xs sm:text-[13px] text-gray-600 dark:text-gray-300 leading-relaxed text-center" x-text="message"></p>
        <template x-if="note">
          <p class="text-xs text-rose-600 dark:text-rose-400 font-semibold leading-relaxed text-center" x-text="note"></p>
        </template>
      </div>

      <!-- Action Button -->
      <div>
        <template x-if="redirectUrl">
          <a :href="redirectUrl" class="w-full py-3 px-4 bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs sm:text-sm rounded-xl shadow-xs transition hover:scale-[1.01] flex items-center justify-center cursor-pointer" x-text="buttonText"></a>
        </template>
        <template x-if="!redirectUrl">
          <button type="button" @click="closeModal()" class="w-full py-3 px-4 bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs sm:text-sm rounded-xl shadow-xs transition hover:scale-[1.01] flex items-center justify-center cursor-pointer" x-text="buttonText"></button>
        </template>
      </div>

    </div>
  </div>
</div>

<script>
  window.__lensmatchAccessConfig = @json([
    'holdPayload' => $__holdPopupPayload,
  ]);

  window.openPhotographerHoldModal = function(actionPhrase) {
    const cfg = window.__lensmatchAccessConfig?.holdPayload;
    if (!cfg) return;

    const action = actionPhrase || 'mengakses fitur ini';
    let message = '';
    let note = '';

    if (cfg.state === 'rejected') {
      message = 'Pendaftaran akun fotografer Anda ditolak untuk ' + action + '. Perbaiki profil Anda sekarang.';
      if (cfg.catatan) {
        note = 'Catatan Admin: ' + cfg.catatan;
      }
    } else if (cfg.state === 'incomplete') {
      if (cfg.missingCount && cfg.missingCount > 0) {
        message = 'Akun fotografer Anda masih kurang ' + cfg.missingCount + ' data untuk ' + action + '. Lengkapi profil terlebih dahulu.';
      } else {
        message = 'Lengkapi profil fotografer Anda terlebih dahulu untuk ' + action + '.';
      }
    } else {
      message = 'Profil fotografer Anda sedang menunggu persetujuan Admin sebelum dapat ' + action + '.';
    }

    window.dispatchEvent(new CustomEvent('open-access-modal', {
      detail: {
        title: cfg.title,
        message: message,
        note: note,
        buttonText: cfg.buttonText,
        redirectUrl: cfg.redirectUrl
      }
    }));
  };

  window.openClientOnlyModal = function(actionPhrase) {
    const action = actionPhrase || 'ini';
    window.dispatchEvent(new CustomEvent('open-access-modal', {
      detail: {
        title: 'Khusus Akun Klien',
        message: 'Fitur ' + action + ' hanya tersedia untuk akun Klien.',
        note: '',
        buttonText: 'Mengerti',
        redirectUrl: ''
      }
    }));
  };
</script>

