<!-- Universal Toast Notification Component (Custom Modern Flat Style with Animated Shrinking Progress Indicator Line) -->
<div 
  x-data="{
    toasts: [],
    addToast(data) {
      const id = Date.now();
      const duration = data.duration || 4500;
      const type = data.type || 'success';

      let accentColor = '#10b981'; // emerald-500
      if (type === 'error') accentColor = '#ef4444'; // red-500
      else if (type === 'warning') accentColor = '#f59e0b'; // amber-500
      else if (type === 'info') accentColor = '#3b82f6'; // blue-500

      const toast = {
        id: id,
        type: type,
        message: data.message || 'Tindakan berhasil diproses.',
        progress: 100,
        accentColor: accentColor,
        interval: null,
      };

      this.toasts.push(toast);

      // Start Countdown Progress Line Animation
      const stepMs = 30;
      const stepDecrement = (stepMs / duration) * 100;

      const targetToast = this.toasts.find(t => t.id === id);
      if (targetToast) {
        targetToast.interval = setInterval(() => {
          targetToast.progress -= stepDecrement;
          if (targetToast.progress <= 0) {
            clearInterval(targetToast.interval);
            this.removeToast(id);
          }
        }, stepMs);
      }
    },
    removeToast(id) {
      const toast = this.toasts.find(t => t.id === id);
      if (toast && toast.interval) {
        clearInterval(toast.interval);
      }
      this.toasts = this.toasts.filter(t => t.id !== id);
    }
  }"
  x-init="
    @if(session('success'))
      addToast({ type: 'success', message: '{{ addslashes(session('success')) }}' });
    @endif
    @if(session('error'))
      addToast({ type: 'error', message: '{{ addslashes(session('error')) }}' });
    @endif
    @if(session('warning'))
      addToast({ type: 'warning', message: '{{ addslashes(session('warning')) }}' });
    @endif
    @if(session('info'))
      addToast({ type: 'info', message: '{{ addslashes(session('info')) }}' });
    @endif
  "
  @show-toast.window="addToast($event.detail)"
  class="fixed top-20 right-5 z-50 space-y-3.5 max-w-sm w-full pointer-events-none"
>
  <template x-for="t in toasts" :key="t.id">
    <div 
      x-transition:enter="transition ease-out duration-300 transform"
      x-transition:enter-start="translate-y-[-20px] opacity-0 scale-95"
      x-transition:enter-end="translate-y-0 opacity-100 scale-100"
      x-transition:leave="transition ease-in duration-200 transform"
      x-transition:leave-start="translate-y-0 opacity-100 scale-100"
      x-transition:leave-end="translate-y-[-20px] opacity-0 scale-95"
      class="relative pointer-events-auto px-5 py-4 rounded-2xl bg-white dark:bg-gray-800 shadow-xl border border-gray-100 dark:border-gray-700/80 flex items-center justify-between gap-4 overflow-hidden"
    >
      <div class="flex items-center gap-3.5 min-w-0 pb-1">
        <!-- Outlined Circular Vector SVG Icon -->
        <div class="shrink-0">
          <template x-if="t.type === 'success'">
            <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
              <circle cx="12" cy="12" r="9" stroke="currentColor" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.5l2 2 4-4" />
            </svg>
          </template>
          <template x-if="t.type === 'error'">
            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
              <circle cx="12" cy="12" r="9" stroke="currentColor" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M9.5 9.5l5 5m0-5l-5 5" />
            </svg>
          </template>
          <template x-if="t.type === 'warning'">
            <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
              <circle cx="12" cy="12" r="9" stroke="currentColor" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4.5m0 3h.01" />
            </svg>
          </template>
          <template x-if="t.type === 'info'">
            <svg class="w-6 h-6 text-gray-800 dark:text-gray-200" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
              <circle cx="12" cy="12" r="9" stroke="currentColor" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 11.5v4.5m0-7h.01" />
            </svg>
          </template>
        </div>

        <!-- Toast Message Text -->
        <p class="text-xs font-semibold text-gray-800 dark:text-gray-100 leading-snug truncate" x-text="t.message"></p>
      </div>

      <!-- Close Button Cross -->
      <button type="button" @click="removeToast(t.id)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition p-1 shrink-0 pb-1">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
      </button>

      <!-- Animated Shrinking Bottom Accent Line Indicator -->
      <div 
        class="absolute bottom-0 left-0 h-[3.5px] transition-all ease-linear"
        :style="'width: ' + t.progress + '%; background-color: ' + t.accentColor"
      ></div>
    </div>
  </template>
</div>