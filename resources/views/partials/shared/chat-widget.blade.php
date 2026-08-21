<!-- Floating Chat Widget with Clean Chat Vector Icon -->
<div x-data="{ open: false }" class="fixed bottom-6 right-6 z-50">
  <button @click="open = !open" 
          class="flex items-center justify-center w-14 h-14 bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold rounded-full shadow-xl transition transform hover:scale-105 border-0 focus:outline-none"
          title="Live Chat Support">
    <template x-if="!open">
      <svg class="w-6 h-6 text-gray-900" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
      </svg>
    </template>
    <template x-if="open">
      <svg class="w-6 h-6 text-gray-900" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
      </svg>
    </template>
  </button>

  <div x-show="open" x-cloak x-transition class="absolute bottom-16 right-0 w-80 sm:w-96 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="bg-amber-400 text-gray-900 font-black px-4 py-3 flex justify-between items-center">
      <span class="font-extrabold text-sm">Real-time Chat LensMatch</span>
      <button @click="open = false" class="text-gray-900 hover:text-black font-black text-sm">✕</button>
    </div>
    <div class="p-4 h-64 overflow-y-auto space-y-3 text-xs">
      <div class="bg-gray-100 dark:bg-gray-700 p-3 rounded-2xl max-w-[85%] text-gray-800 dark:text-gray-200 font-medium leading-relaxed">
        Halo! Ada yang bisa kami bantu seputar booking fotografer atau diskusi komunitas?
      </div>
    </div>
    <div class="p-3 border-t border-gray-200 dark:border-gray-700 flex gap-2 bg-gray-50 dark:bg-gray-900">
      <input type="text" placeholder="Ketik pesan..." class="w-full text-xs px-3.5 py-2.5 border border-gray-200 rounded-xl dark:bg-gray-800 dark:border-gray-600 dark:text-white outline-none focus:ring-2 focus:ring-amber-500" />
      <button class="bg-amber-400 hover:bg-amber-500 text-gray-900 text-xs px-4 py-2.5 rounded-xl font-extrabold shadow-xs transition">Kirim</button>
    </div>
  </div>
</div>