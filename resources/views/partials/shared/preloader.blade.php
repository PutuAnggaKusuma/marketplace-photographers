<div
  x-show="loaded"
  x-init="
    window.__appPreloaderVisible = true;
    const hidePreloader = () => {
      setTimeout(() => {
        loaded = false;
        window.__appPreloaderVisible = false;
        window.dispatchEvent(new CustomEvent('app:preloader-hidden'));
      }, 500);
    };
    if (document.readyState === 'loading') {
      window.addEventListener('DOMContentLoaded', hidePreloader, { once: true });
    } else {
      hidePreloader();
    }
  "
  class="fixed left-0 top-0 z-999999 flex h-screen w-screen items-center justify-center bg-white dark:bg-black"
>
  <div
    class="h-16 w-16 animate-spin rounded-full border-4 border-solid border-brand-500 border-t-transparent"
  ></div>
</div>
