<!-- Floating Chat Widget -->
<div x-data="{ open: false }" class="fixed bottom-6 right-6 z-50">
  <button @click="open = !open" class="flex items-center justify-center w-14 h-14 bg-brand-500 hover:bg-brand-600 text-white rounded-full shadow-lg transition transform hover:scale-105">
    💬
  </button>

  <div x-show="open" x-transition class="absolute bottom-16 right-0 w-80 sm:w-96 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="bg-brand-500 text-white px-4 py-3 flex justify-between items-center">
      <span class="font-semibold text-sm">Real-time Chat</span>
      <button @click="open = false" class="text-white text-xs opacity-75 hover:opacity-100">✕</button>
    </div>
    <div class="p-4 h-64 overflow-y-auto space-y-3 text-xs">
      <div class="bg-gray-100 dark:bg-gray-700 p-2.5 rounded-lg max-w-[80%]">
        Halo! Ada yang bisa kami bantu seputar booking fotografer?
      </div>
    </div>
    <div class="p-3 border-t border-gray-200 dark:border-gray-700 flex gap-2">
      <input type="text" placeholder="Ketik pesan..." class="w-full text-xs px-3 py-2 border rounded-lg dark:bg-gray-900 dark:border-gray-600 dark:text-white" />
      <button class="bg-brand-500 text-white text-xs px-3 py-2 rounded-lg font-medium">Kirim</button>
    </div>
  </div>
</div>
