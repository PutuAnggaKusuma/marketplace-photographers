@extends('layouts.app')

@section('title', 'Pusat Bantuan & FAQ - LensMatch')

@section('content')
<section class="py-12 bg-white dark:bg-gray-900/50 min-h-screen">
  <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10" x-data="{ activeTab: 'booking', activeKey: null }">

    <!-- Page Header Banner -->
    <div class="bg-gradient-to-r from-gray-900 via-amber-950 to-gray-900 text-white rounded-3xl p-8 sm:p-12 shadow-xl border border-amber-500/20 text-center space-y-4">
      <span class="px-3.5 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-amber-400 text-gray-900">
        Pusat Informasi & Bantuan
      </span>
      <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white tracking-tight leading-tight">
        Ada yang Bisa Kami Bantu?
      </h1>
      <p class="text-xs sm:text-sm text-gray-300 max-w-xl mx-auto leading-relaxed">
        Temukan jawaban lengkap seputar cara booking sesi foto, garansi pembayaran escrow, verifikasi studio fotografer, dan kompetisi lomba foto.
      </p>
    </div>

    <!-- Category Nav Tabs -->
    <div class="flex items-center justify-center gap-2 overflow-x-auto pb-2">
      @foreach($faqs as $catKey => $catData)
        <button type="button" 
                @click="activeTab = '{{ $catKey }}'; activeKey = null" 
                class="px-5 py-2.5 rounded-2xl text-xs font-extrabold transition shrink-0"
                :class="activeTab === '{{ $catKey }}' ? 'bg-amber-500 text-white shadow-md' : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-100'">
          {{ $catData['title'] }}
        </button>
      @endforeach
    </div>

    <!-- FAQ Accordion List -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-200 dark:border-gray-700/80 p-6 sm:p-10 shadow-sm space-y-4">
      @foreach($faqs as $catKey => $catData)
        <div x-show="activeTab === '{{ $catKey }}'" x-cloak class="space-y-4">
          <h3 class="text-base font-black text-amber-600 dark:text-amber-400 mb-6 border-b border-gray-100 dark:border-gray-700 pb-3">
            {{ $catData['title'] }}
          </h3>

          <div class="space-y-3">
            @foreach($catData['items'] as $index => $item)
              @php $key = $catKey . '_' . $index; @endphp
              <div class="rounded-2xl border border-gray-200/80 dark:border-gray-700 overflow-hidden bg-white dark:bg-gray-900/40 transition">
                <button type="button" 
                        @click="activeKey = (activeKey === '{{ $key }}' ? null : '{{ $key }}')" 
                        class="w-full px-5 py-4 text-left font-extrabold text-xs sm:text-sm text-gray-900 dark:text-white flex items-center justify-between gap-4">
                  <span>{{ $item['q'] }}</span>
                  <span class="p-1 rounded-lg bg-gray-200 dark:bg-gray-800 text-gray-600 dark:text-gray-300 shrink-0 transition-transform duration-200"
                        :class="activeKey === '{{ $key }}' ? 'rotate-180 bg-amber-500 text-white' : ''">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                  </span>
                </button>

                <div x-show="activeKey === '{{ $key }}'" 
                     x-collapse 
                     class="px-5 pb-5 text-xs text-gray-600 dark:text-gray-300 leading-relaxed border-t border-gray-100 dark:border-gray-800 pt-3">
                  {{ $item['a'] }}
                </div>
              </div>
            @endforeach
          </div>
        </div>
      @endforeach
    </div>

    <!-- Live Customer Support Contact Box -->
    <div class="bg-gradient-to-r from-amber-500/10 via-amber-400/5 to-amber-500/10 p-8 rounded-3xl border border-amber-500/30 text-center space-y-4">
      <h3 class="text-xl font-black text-gray-900 dark:text-white">Masih Memiliki Pertanyaan Lain?</h3>
      <p class="text-xs text-gray-600 dark:text-gray-400 max-w-md mx-auto">Tim Customer Support LensMatch siap membantu Anda 24/7 melalui layanan obrolan langsung WhatsApp atau Email.</p>
      <div class="flex items-center justify-center gap-3 pt-2">
        <a href="https://wa.me/6281234567890" target="_blank" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl shadow-xs transition flex items-center gap-2">
          <span>Hubungi via WhatsApp</span>
        </a>
        <a href="mailto:support@lensmatch.com" class="px-5 py-2.5 bg-gray-900 dark:bg-white dark:text-gray-900 text-white font-extrabold text-xs rounded-xl shadow-xs transition flex items-center gap-2">
          <span>Kirim Email Support</span>
        </a>
      </div>
    </div>

  </div>
</section>
@endsection