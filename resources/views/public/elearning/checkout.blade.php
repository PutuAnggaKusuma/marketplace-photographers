@extends('layouts.app')

@section('title', 'Checkout — ' . $course->judul . ' — LensMatch')

@section('content')
<section class="flex-1 w-full bg-white dark:bg-gray-900 flex flex-col justify-center py-8 sm:py-12 lg:py-16">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full" x-data="{ selectedMethod: 'qris' }">

    <!-- Page Title -->
    <div class="mb-6">
      <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">
        Checkout
      </h1>
    </div>

    <form action="{{ route('public.elearning.checkout.process', $course->slug) }}" method="POST">
      @csrf
      <input type="hidden" name="payment_method" :value="selectedMethod">

      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">
        
        <!-- LEFT COLUMN: Item Details & Payment Methods (7 Cols) -->
        <div class="lg:col-span-7 flex flex-col justify-between gap-6">
          
          <!-- Course Item Card (Top Aligned with Right Summary Card) -->
          <div class="p-4 sm:p-5 rounded-2xl bg-gray-50/80 dark:bg-gray-850/70 border border-gray-200/80 dark:border-gray-800 flex flex-col sm:flex-row items-start sm:items-center gap-4">
            <img 
              src="{{ $course->thumbnail_url ?: asset('images/logo/lensmatch_logo_transparent_yellow.png') }}" 
              alt="{{ $course->judul }}" 
              class="w-full sm:w-36 h-28 sm:h-24 object-cover rounded-xl shrink-0" 
            />
            <div class="space-y-1.5 flex-1 min-w-0">
              <span class="inline-block px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200/80 dark:border-amber-800/60">
                {{ $course->kategori }}
              </span>
              <h3 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white leading-snug">
                {{ $course->judul }}
              </h3>
              <div class="flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400 pt-0.5">
                <span>{{ $course->modules->count() }} Modul</span>
                <span>•</span>
                <span>{{ $course->modules->flatMap->lessons->count() }} Materi</span>
                @if($course->level)
                  <span>•</span>
                  <span class="capitalize">{{ $course->level }}</span>
                @endif
              </div>
            </div>
          </div>

          <!-- Payment Methods -->
          <div class="space-y-3">
            <h2 class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
              Metode Pembayaran
            </h2>

            <div class="space-y-3">
              <!-- QRIS -->
              <label 
                @click="selectedMethod = 'qris'" 
                class="p-4 rounded-2xl border transition cursor-pointer flex items-center justify-between gap-4 select-none bg-white dark:bg-gray-850"
                :class="selectedMethod === 'qris' ? 'border-amber-400 bg-amber-50/30 dark:bg-amber-950/20 shadow-xs' : 'border-gray-200 dark:border-gray-750 hover:border-gray-300 dark:hover:border-gray-650'"
              >
                <div class="space-y-1 min-w-0">
                  <div class="flex items-center gap-2">
                    <span class="text-sm font-bold text-gray-900 dark:text-white">QRIS</span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">Instan</span>
                  </div>
                  <p class="text-xs text-gray-500 dark:text-gray-400">GoPay, OVO, Dana, ShopeePay, BCA, Livin', BRImo, dan m-Banking lainnya</p>
                </div>
                <span class="w-4 h-4 rounded-full border-2 flex items-center justify-center shrink-0 transition" :class="selectedMethod === 'qris' ? 'border-amber-400 bg-white dark:bg-gray-900' : 'border-gray-300 dark:border-gray-600'">
                  <span class="w-2 h-2 rounded-full bg-amber-400" x-show="selectedMethod === 'qris'"></span>
                </span>
              </label>

              <!-- Virtual Account -->
              <label 
                @click="selectedMethod = 'bank'" 
                class="p-4 rounded-2xl border transition cursor-pointer flex items-center justify-between gap-4 select-none bg-white dark:bg-gray-850"
                :class="selectedMethod === 'bank' ? 'border-amber-400 bg-amber-50/30 dark:bg-amber-950/20 shadow-xs' : 'border-gray-200 dark:border-gray-750 hover:border-gray-300 dark:hover:border-gray-650'"
              >
                <div class="space-y-1 min-w-0">
                  <div class="flex items-center gap-2">
                    <span class="text-sm font-bold text-gray-900 dark:text-white">Virtual Account (VA)</span>
                  </div>
                  <p class="text-xs text-gray-500 dark:text-gray-400">BCA, Mandiri, BNI, BRI, Permata, & CIMB Niaga</p>
                </div>
                <span class="w-4 h-4 rounded-full border-2 flex items-center justify-center shrink-0 transition" :class="selectedMethod === 'bank' ? 'border-amber-400 bg-white dark:bg-gray-900' : 'border-gray-300 dark:border-gray-600'">
                  <span class="w-2 h-2 rounded-full bg-amber-400" x-show="selectedMethod === 'bank'"></span>
                </span>
              </label>

              <!-- E-Wallet -->
              <label 
                @click="selectedMethod = 'ewallet'" 
                class="p-4 rounded-2xl border transition cursor-pointer flex items-center justify-between gap-4 select-none bg-white dark:bg-gray-850"
                :class="selectedMethod === 'ewallet' ? 'border-amber-400 bg-amber-50/30 dark:bg-amber-950/20 shadow-xs' : 'border-gray-200 dark:border-gray-750 hover:border-gray-300 dark:hover:border-gray-650'"
              >
                <div class="space-y-1 min-w-0">
                  <div class="flex items-center gap-2">
                    <span class="text-sm font-bold text-gray-900 dark:text-white">E-Wallet</span>
                  </div>
                  <p class="text-xs text-gray-500 dark:text-gray-400">GoPay, ShopeePay, OVO, DANA, LinkAja</p>
                </div>
                <span class="w-4 h-4 rounded-full border-2 flex items-center justify-center shrink-0 transition" :class="selectedMethod === 'ewallet' ? 'border-amber-400 bg-white dark:bg-gray-900' : 'border-gray-300 dark:border-gray-600'">
                  <span class="w-2 h-2 rounded-full bg-amber-400" x-show="selectedMethod === 'ewallet'"></span>
                </span>
              </label>
            </div>
          </div>

        </div>

        <!-- RIGHT COLUMN: Order Summary (5 Cols - Stretched to Match Bottom) -->
        <div class="lg:col-span-5 flex flex-col h-full">
          <div class="p-6 rounded-2xl bg-gray-50/80 dark:bg-gray-850 border border-gray-200/80 dark:border-gray-800 h-full flex flex-col justify-between gap-6">
            
            <div class="space-y-4">
              <h2 class="text-xs font-bold uppercase tracking-wider text-gray-900 dark:text-white">
                Ringkasan Pesanan
              </h2>

              <!-- Item Row -->
              <div class="flex items-start justify-between gap-3 text-xs text-gray-600 dark:text-gray-300 pb-2">
                <span class="font-medium line-clamp-2">{{ $course->judul }}</span>
                <span class="font-bold text-gray-900 dark:text-white shrink-0">
                  Rp {{ number_format($course->harga ?: 0, 0, ',', '.') }}
                </span>
              </div>

              <!-- Price Breakdown -->
              <div class="space-y-2.5 text-xs text-gray-600 dark:text-gray-400 pt-2">
                <div class="flex items-center justify-between">
                  <span>Harga Kursus</span>
                  <span class="text-gray-900 dark:text-gray-200">Rp {{ number_format($course->harga ?: 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between">
                  <span>Biaya Layanan</span>
                  <span class="text-emerald-600 font-semibold">Rp 0</span>
                </div>
                <div class="flex items-center justify-between pt-2">
                  <span class="text-sm font-bold text-gray-900 dark:text-white">Total Tagihan</span>
                  <span class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white">
                    Rp {{ number_format($course->harga ?: 0, 0, ',', '.') }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Action CTA & Trust Note (Pushed to bottom) -->
            <div class="space-y-3 pt-4">
              <button 
                type="submit" 
                class="w-full py-3 text-xs font-bold text-gray-900 bg-amber-400 hover:bg-amber-500 rounded-xl transition cursor-pointer text-center block"
              >
                Bayar Sekarang
              </button>

              <div class="text-center">
                <p class="text-[11px] text-gray-400 flex items-center justify-center gap-1.5">
                  <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
                  <span>Pembayaran aman & terenkripsi</span>
                </p>
              </div>
            </div>

          </div>
        </div>

      </div>
    </form>
  </div>
</section>
@endsection
