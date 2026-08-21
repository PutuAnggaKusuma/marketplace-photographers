@extends('layouts.app')

@section('title', $course->judul . ' — Akademi LensMatch')

@section('content')
<section class="py-12 bg-white dark:bg-gray-900/50 min-h-screen">
  <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

    <!-- Back Navigation -->
    <div>
      <a href="{{ route('public.elearning.index') }}" class="text-xs font-bold text-gray-500 hover:text-amber-500 transition inline-flex items-center gap-1">
        <span>← Kembali ke Katalog Akademi</span>
      </a>
    </div>

    <!-- Course Header Banner -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm space-y-6 p-6 sm:p-10">
      <div class="flex flex-wrap items-center gap-3">
        <span class="px-3 py-1 bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300 text-[11px] font-black rounded-lg uppercase">
          {{ $course->kategori }}
        </span>
        <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-[11px] font-bold rounded-lg">
          Level: {{ $course->level }}
        </span>
        <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-[11px] font-bold rounded-lg">
          Durasi: {{ $course->durasi }}
        </span>
      </div>

      <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-gray-900 dark:text-white leading-tight">
        {{ $course->judul }}
      </h1>

      <p class="text-sm text-gray-600 dark:text-gray-300 italic bg-amber-50/60 dark:bg-amber-950/30 p-4 rounded-xl border border-amber-200/60">
        "{{ $course->ringkasan }}"
      </p>

      <div class="rounded-2xl overflow-hidden h-72 sm:h-96 border border-gray-200 dark:border-gray-700">
        <img onerror="this.onerror=null;this.src=window.SVG_IMAGE_FALLBACK||'data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='800' height='600' viewBox='0 0 800 600' fill='none'%3E%3Crect width='800' height='600' fill='%23F3F4F6'/%3E%3Cpath d='M360 260C360 248.954 368.954 240 380 240H420C431.046 240 440 248.954 440 260V265H450C466.569 265 480 278.431 480 295V345C480 361.569 466.569 375 450 375H350C333.431 375 320 361.569 320 345V295C320 278.431 333.431 265 350 265H360V260Z' stroke='%239CA3AF' stroke-width='12' stroke-linecap='round' stroke-linejoin='round'/%3E%3Ccircle cx='400' cy='320' r='30' stroke='%239CA3AF' stroke-width='12'/%3E%3Cline x1='310' y1='240' x2='490' y2='390' stroke='%23EF4444' stroke-width='10' stroke-linecap='round'/%3E%3Ctext x='400' y='450' font-family='sans-serif' font-size='22' font-weight='700' fill='%236B7280' text-anchor='middle'%3EGambar Tidak Dapat Dimuat%3C/text%3E%3C/svg%3E';" src="{{ $course->thumbnail_url }}" alt="{{ $course->judul }}" class="w-full h-full object-cover" />
      </div>

      <!-- Main Course Body Content -->
      <div class="prose dark:prose-invert max-w-none text-sm text-gray-800 dark:text-gray-200 leading-relaxed space-y-4 pt-4 border-t border-gray-100 dark:border-gray-700">
        {!! nl2br(e($course->konten)) !!}
      </div>
    </div>

    <!-- Related Courses Sidebar -->
    @if($relatedCourses->count() > 0)
      <div class="space-y-4 pt-4">
        <h3 class="text-lg font-black text-gray-900 dark:text-white">Materi Terkait Lainnya</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          @foreach($relatedCourses as $rc)
            <a href="{{ route('public.elearning.show', $rc->slug) }}" class="bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-200 dark:border-gray-700 hover:shadow-md transition space-y-2 block">
              <span class="text-[10px] font-bold text-amber-600 uppercase">{{ $rc->kategori }}</span>
              <h4 class="font-extrabold text-xs text-gray-900 dark:text-white line-clamp-2">{{ $rc->judul }}</h4>
            </a>
          @endforeach
        </div>
      </div>
    @endif

  </div>
</section>
@endsection