@extends('layouts.app')

@section('title', 'Pusat Bantuan — LensMatch')

@section('content')
<section class="pt-10 pb-16 lg:pt-12 lg:pb-24 bg-white dark:bg-gray-900 min-h-screen">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 lg:space-y-10" x-data="{ activeTab: 'booking', activeKey: null }">

    <!-- Header Section -->
    <div class="space-y-2">
      <h1 class="text-3xl sm:text-4xl lg:text-[40px] font-black text-gray-900 dark:text-white tracking-tight leading-tight">
        Pusat Bantuan
      </h1>
      <p class="text-sm text-gray-500 dark:text-gray-400 max-w-2xl leading-relaxed">
        Panduan reservasi sesi foto, pembayaran, verifikasi fotografer, dan lomba foto.
      </p>
    </div>

    <!-- Category Nav Tabs (Rule #78 Horizontal Underline Tabs) -->
    <div class="flex items-center gap-6 overflow-x-auto pb-1">
      @foreach($faqs as $catKey => $catData)
        <button type="button"
                @click="activeTab = '{{ $catKey }}'; activeKey = null"
                class="py-2 text-xs sm:text-sm transition flex items-center gap-2 cursor-pointer shrink-0 focus:outline-none focus:ring-0"
                :class="activeTab === '{{ $catKey }}' ? 'text-amber-600 dark:text-amber-400 font-black' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 font-bold'">
          <span :class="activeTab === '{{ $catKey }}' ? 'underline underline-offset-8 decoration-2 decoration-amber-500 dark:decoration-amber-400' : ''">{{ $catData['title'] }}</span>
          <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold"
                :class="activeTab === '{{ $catKey }}' ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300'">
            {{ count($catData['items'] ?? []) }}
          </span>
        </button>
      @endforeach
    </div>

    <!-- FAQ Accordion Container -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200/80 dark:border-gray-700/80 p-6 sm:p-8 space-y-5">
      @foreach($faqs as $catKey => $catData)
        <div x-show="activeTab === '{{ $catKey }}'" x-cloak class="space-y-4">
          <h2 class="text-base sm:text-lg font-black text-gray-900 dark:text-white tracking-tight">
            {{ $catData['title'] }}
          </h2>

          <div class="space-y-3">
            @foreach($catData['items'] as $index => $item)
              @php $key = $catKey . '_' . $index; @endphp
              <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200/80 dark:border-gray-700/80 shadow-none overflow-hidden transition hover:border-amber-400/80">
                <button type="button"
                        @click="activeKey = (activeKey === '{{ $key }}' ? null : '{{ $key }}')"
                        class="w-full p-4 sm:p-5 text-left flex items-center justify-between gap-3 cursor-pointer hover:bg-gray-50/50 dark:hover:bg-gray-700/50 transition focus:outline-none focus:ring-0">
                  <span class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2.5 transition-colors"
                        :class="activeKey === '{{ $key }}' ? 'text-amber-600 dark:text-amber-400' : ''">
                    <svg class="w-4 h-4 text-amber-500 dark:text-amber-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
                    </svg>
                    <span>{{ $item['q'] }}</span>
                  </span>
                  <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 shrink-0"
                       :class="activeKey === '{{ $key }}' ? 'rotate-180 text-amber-500' : ''"
                       fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                  </svg>
                </button>

                <div x-show="activeKey === '{{ $key }}'"
                     x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="px-4 sm:px-5 pb-4 sm:pb-5 pt-0.5">
                  <div class="text-xs sm:text-sm leading-relaxed text-gray-600 dark:text-gray-300 pl-6 sm:pl-7">
                    <p>{{ $item['a'] }}</p>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      @endforeach
    </div>

  </div>
</section>
@endsection
