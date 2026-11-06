<!-- Universal Validation & Confirmation Modal Dialog (Strictly Following Rule #59, #74, #76, #80) -->
<div 
  id="confirm-modal-root"
  x-data="{
    confirmOpen: false,
    modalTitle: 'Konfirmasi Aksi',
    modalMessage: 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
    modalActionUrl: '',
    modalMethod: 'POST',
    modalButtonText: 'Ya, Lanjutkan',
    modalButtonClass: 'bg-red-600 hover:bg-red-700 text-white',
    iconType: 'warning',
    targetForm: null,
    submitButton: null,
    onConfirmCallback: null,
    isSubmitting: false,

    openModal(data) {
      this.modalTitle = data.title || 'Konfirmasi Aksi';
      this.modalMessage = data.message || 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
      this.modalActionUrl = data.actionUrl || '';
      this.modalMethod = data.method || 'POST';
      this.modalButtonText = data.buttonText || data.confirmText || 'Ya, Lanjutkan';
      this.modalButtonClass = data.buttonClass || (data.type === 'save' || data.type === 'primary' ? 'bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold shadow-xs' : (data.type === 'success' ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : 'bg-red-600 hover:bg-red-700 text-white'));
      this.iconType = data.iconType || data.type || 'warning';
      this.targetForm = data.targetForm || null;
      this.submitButton = data.submitButton || null;
      this.onConfirmCallback = data.onConfirm || null;
      this.isSubmitting = false;
      this.confirmOpen = true;
    },

    closeModal() {
      if (this.isSubmitting) return;
      if (this.targetForm) {
        delete this.targetForm.__isPendingConfirm;
      }
      this.onConfirmCallback = null;
      this.confirmOpen = false;
    },

    executeConfirm() {
      if (this.isSubmitting) return;
      this.isSubmitting = true;

      if (typeof this.onConfirmCallback === 'function') {
        const cb = this.onConfirmCallback;
        this.confirmOpen = false;
        this.onConfirmCallback = null;
        setTimeout(() => cb(), 50);
        return;
      }

      if (this.targetForm) {
        const formToSubmit = this.targetForm;
        const btnToSubmit = this.submitButton;
        delete formToSubmit.__isPendingConfirm;
        formToSubmit.dataset.confirmed = 'true';
        this.confirmOpen = false;

        // Give Alpine a tick to close cleanly then submit
        setTimeout(() => {
          if (typeof formToSubmit.requestSubmit === 'function') {
            try {
              formToSubmit.requestSubmit(btnToSubmit || undefined);
            } catch (err) {
              formToSubmit.submit();
            }
          } else {
            formToSubmit.submit();
          }
        }, 50);
      } else {
        // Direct Action URL Form Submit
        const formEl = this.$refs.actionUrlForm;
        if (formEl) {
          formEl.submit();
        }
      }
    }
  }"
  @open-confirm-modal.window="openModal($event.detail)"
  x-show="confirmOpen"
  x-cloak
  @keydown.escape.window="closeModal()"
  class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
  x-transition:enter="transition ease-out duration-200"
  x-transition:enter-start="opacity-0"
  x-transition:enter-end="opacity-100"
  x-transition:leave="transition ease-in duration-150"
  x-transition:leave-start="opacity-100"
  x-transition:leave-end="opacity-0"
>
  <div @click.away="closeModal()" 
       class="bg-white dark:bg-gray-800 max-w-md w-full p-6 sm:p-7 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-2xl space-y-5 text-center transform transition-all"
       x-transition:enter="transition ease-out duration-200"
       x-transition:enter-start="opacity-0 scale-95"
       x-transition:enter-end="opacity-100 scale-100"
       x-transition:leave="transition ease-in duration-150"
       x-transition:leave-start="opacity-100 scale-100"
       x-transition:leave-end="opacity-0 scale-95">
    
    <!-- Modal Title & Description (Strictly Rule #59: Centered, No Icon) -->
    <div class="space-y-2 text-center">
      <h3 class="text-base sm:text-lg font-black text-gray-900 dark:text-white text-center tracking-tight" x-text="modalTitle"></h3>
      <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed text-center" x-text="modalMessage"></p>
    </div>

    <!-- Case 1: Target Form Submission (Simpan / Perbaharui Data) -->
    <template x-if="targetForm">
      <div class="flex items-center justify-center gap-3 pt-2">
        <button type="button" 
                @click="closeModal()" 
                :disabled="isSubmitting"
                class="py-2.5 px-5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 hover:bg-gray-100 dark:bg-gray-700 text-xs font-bold text-gray-700 dark:text-gray-300 transition cursor-pointer disabled:opacity-50">
          Batal
        </button>
        <button type="button" 
                @click="executeConfirm()" 
                :disabled="isSubmitting"
                :class="modalButtonClass" 
                class="py-2.5 px-6 rounded-xl text-xs font-extrabold shadow-xs transition cursor-pointer disabled:opacity-50">
          <span x-text="isSubmitting ? 'Memproses...' : modalButtonText"></span>
        </button>
      </div>
    </template>

    <!-- Case 3: JavaScript Callback Submission -->
    <template x-if="onConfirmCallback && !targetForm">
      <div class="flex items-center justify-center gap-3 pt-2">
        <button type="button" 
                @click="closeModal()" 
                :disabled="isSubmitting"
                class="py-2.5 px-5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 hover:bg-gray-100 dark:bg-gray-700 text-xs font-bold text-gray-700 dark:text-gray-300 transition cursor-pointer disabled:opacity-50">
          Batal
        </button>
        <button type="button" 
                @click="executeConfirm()" 
                :disabled="isSubmitting"
                :class="modalButtonClass" 
                class="py-2.5 px-6 rounded-xl text-xs font-extrabold shadow-xs transition cursor-pointer disabled:opacity-50">
          <span x-text="isSubmitting ? 'Memproses...' : modalButtonText"></span>
        </button>
      </div>
    </template>

    <!-- Case 2: Action URL Submission (Hapus / Status Change) -->
    <template x-if="!targetForm && !onConfirmCallback">
      <form x-ref="actionUrlForm" :action="modalActionUrl" :method="modalMethod === 'GET' ? 'GET' : 'POST'" class="space-y-3">
        @csrf
        <template x-if="modalMethod !== 'GET' && modalMethod !== 'POST'">
          <input type="hidden" name="_method" :value="modalMethod">
        </template>

        <div class="flex items-center justify-center gap-3 pt-2">
          <button type="button" 
                  @click="closeModal()" 
                  :disabled="isSubmitting"
                  class="py-2.5 px-5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 hover:bg-gray-100 dark:bg-gray-700 text-xs font-bold text-gray-700 dark:text-gray-300 transition cursor-pointer disabled:opacity-50">
            Batal
          </button>
          <button type="submit" 
                  @click="isSubmitting = true"
                  :disabled="isSubmitting"
                  :class="modalButtonClass" 
                  class="py-2.5 px-6 rounded-xl text-xs font-extrabold shadow-xs transition cursor-pointer disabled:opacity-50">
            <span x-text="isSubmitting ? 'Memproses...' : modalButtonText"></span>
          </button>
        </div>
      </form>
    </template>

  </div>
</div>

<!-- Universal Pre-Validation & Form Confirmation Interceptor -->
<script>
(function() {
  if (window.__universalFormConfirmInitialized) return;
  window.__universalFormConfirmInitialized = true;

  function handleFormInterception(form, submitBtn, e) {
    if (!form || !form.tagName || form.tagName.toLowerCase() !== 'form') return false;

    // 1. Skip GET forms (search bars, filter forms, sort dropdowns)
    if (form.method && form.method.toUpperCase() === 'GET') return false;

    // 2. Skip if the form has already been confirmed by user in modal
    if (form.dataset.confirmed === 'true') {
      delete form.dataset.confirmed;
      delete form.__isPendingConfirm;
      return false; // Proceed with actual submission
    }

    // 3. Skip if form is already awaiting confirmation modal
    if (form.__isPendingConfirm) {
      if (e) { e.preventDefault(); e.stopPropagation(); }
      return true;
    }

    // 4. Skip forms explicitly marked to opt out
    if (form.hasAttribute('data-no-confirm') || (submitBtn && submitBtn.hasAttribute('data-no-confirm'))) {
      return false;
    }

    // 5. Skip chat message forms & inline searches
    if (form.id === 'chat-send-form' || form.closest('#chat-form') || form.closest('#chat-box')) {
      return false;
    }

    // 6. Skip if form is already located inside an existing modal (unless explicitly opted-in with data-confirm)
    const insideModal = form.closest('[role="dialog"], [x-show*="Modal"], [x-show*="modal"], .fixed.inset-0:not(#confirm-modal-root)');
    if (insideModal && !form.hasAttribute('data-confirm') && (!submitBtn || !submitBtn.hasAttribute('data-confirm'))) {
      return false;
    }

    // 7. Check if this form represents a Save, Update, or Create action
    const btnText = submitBtn ? (submitBtn.innerText || submitBtn.value || '').trim().toLowerCase() : '';
    const isSaveAction = btnText.includes('simpan') || btnText.includes('save') || btnText.includes('buat') || form.hasAttribute('data-confirm') || (submitBtn && submitBtn.hasAttribute('data-confirm'));
    const isUpdateAction = btnText.includes('perbaharui') || btnText.includes('perbarui') || btnText.includes('update');
    const isSpecialAction = btnText.includes('kirim pengajuan') || btnText.includes('lanjut ke invoice');

    if (!isSaveAction && !isUpdateAction && !isSpecialAction) {
      return false;
    }

    // INTERCEPT: Stop the default submission/click
    if (e) {
      e.preventDefault();
      e.stopPropagation();
    }

    // ==========================================
    // PRE-VALIDATION CHECK: Check HTML5 Validity
    // ==========================================
    const isValid = form.checkValidity();

    if (!isValid) {
      // RULE: JIKA MASIH ADA INPUT TIDAK VALID / KOSONG:
      // MODAL KONFIRMASI DILARANG MUNCUL!
      // Kursor dan layar otomatis berpindah ke input paling atas yang error.
      const invalidElements = form.querySelectorAll(':invalid');
      const firstInvalid = invalidElements.length > 0 ? invalidElements[0] : null;

      if (firstInvalid) {
        // Target container jika elemen input hidden/opacity-0
        let targetScroll = firstInvalid;
        if (firstInvalid.offsetParent === null && firstInvalid.parentElement) {
          targetScroll = firstInvalid.parentElement;
        }

        // Smooth scroll ke elemen invalid pertama
        targetScroll.scrollIntoView({ behavior: 'smooth', block: 'center' });

        // Fokuskan elemen & tampilkan validasi UI bawaan browser
        setTimeout(() => {
          try {
            firstInvalid.focus({ preventScroll: true });
            if (typeof firstInvalid.reportValidity === 'function') {
              firstInvalid.reportValidity();
            } else {
              form.reportValidity();
            }
          } catch (err) {
            form.reportValidity();
          }
        }, 180);
      } else {
        form.reportValidity();
      }
      return true; // Intercepted and blocked
    }

    // =========================================================
    // JIKA 100% VALID: TAMPILKAN MODAL KONFIRMASI STANDAR (RULE #59)
    // =========================================================
    form.__isPendingConfirm = true;

    let modalTitle = 'Konfirmasi Simpan Data';
    let modalMessage = 'Apakah Anda yakin semua data yang dimasukkan sudah benar? Klik Ya untuk menyimpan ke sistem.';
    let modalBtnText = 'Ya, Simpan Data';
    let modalBtnClass = 'bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold shadow-xs';

    if (isUpdateAction) {
      modalTitle = 'Konfirmasi Perubahan Data';
      modalMessage = 'Apakah Anda yakin ingin memperbaharui data ini dengan perubahan yang baru?';
      modalBtnText = 'Ya, Perbaharui Data';
    } else if (btnText.includes('kirim pengajuan')) {
      modalTitle = 'Konfirmasi Pengajuan Lomba';
      modalMessage = 'Apakah Anda yakin seluruh rincian usulan lomba sudah lengkap dan siap dikirim?';
      modalBtnText = 'Ya, Kirim Pengajuan';
    } else if (btnText.includes('lanjut ke invoice')) {
      modalTitle = 'Konfirmasi Reservasi Jadwal';
      modalMessage = 'Apakah jadwal dan detail sesi pemotretan sudah sesuai? Lanjutkan ke penerbitan invoice pembayaran?';
      modalBtnText = 'Ya, Lanjut ke Invoice';
    }

    // Override custom via data attributes
    if (form.dataset.confirmTitle || (submitBtn && submitBtn.dataset.confirmTitle)) {
      modalTitle = form.dataset.confirmTitle || submitBtn.dataset.confirmTitle;
    }
    if (form.dataset.confirmMessage || (submitBtn && submitBtn.dataset.confirmMessage)) {
      modalMessage = form.dataset.confirmMessage || submitBtn.dataset.confirmMessage;
    }
    if (form.dataset.confirmBtn || (submitBtn && submitBtn.dataset.confirmBtn)) {
      modalBtnText = form.dataset.confirmBtn || submitBtn.dataset.confirmBtn;
    }

    // Buka Modal Konfirmasi
    window.dispatchEvent(new CustomEvent('open-confirm-modal', {
      detail: {
        title: modalTitle,
        message: modalMessage,
        buttonText: modalBtnText,
        buttonClass: modalBtnClass,
        type: 'save',
        targetForm: form,
        submitButton: submitBtn
      }
    }));

    return true;
  }

  // Intercept via submit button click (capture phase)
  document.addEventListener('click', function(e) {
    const submitBtn = e.target.closest('button[type="submit"], input[type="submit"]');
    if (!submitBtn) return;

    const form = submitBtn.form || submitBtn.closest('form');
    if (!form) return;

    handleFormInterception(form, submitBtn, e);
  }, true);

  // Intercept via form submit event (e.g. user presses Enter in input field)
  document.addEventListener('submit', function(e) {
    const form = e.target;
    if (!form || !form.tagName || form.tagName.toLowerCase() !== 'form') return;

    const submitBtn = e.submitter || form.querySelector('button[type="submit"], input[type="submit"]');
    handleFormInterception(form, submitBtn, e);
  }, true);
})();
</script>
