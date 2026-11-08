@extends('layouts.admin')



@section('title', 'Kurikulum & Tahapan: ' . $course->judul . ' — Admin LensMatch')



@section('content')

<div 

  x-data="{

    showModuleModal: false,

    showLessonModal: false,

    moduleMode: 'create',

    moduleForm: {

      id: null,

      judul_modul: '',

      deskripsi: '',

      urutan: 1,

      actionUrl: '{{ route('admin.elearning.modules.store', $course->id) }}'

    },

    lessonMode: 'create',

    lessonForm: {

      id: null,

      id_module: '',

      judul: '',

      tipe: 'video',

      konten: '',

      media_url: '',

      durasi_menit: 5,

      urutan: 1,

      is_free: '1',

      actionUrl: '{{ route('admin.elearning.lessons.store', $course->id) }}'

    },

    openCreateModule() {

      this.moduleMode = 'create';

      this.moduleForm = {

        id: null,

        judul_modul: '',

        deskripsi: '',

        urutan: {{ ($course->modules()->max('urutan') ?? 0) + 1 }},

        actionUrl: '{{ route('admin.elearning.modules.store', $course->id) }}'

      };

      this.showModuleModal = true;

    },

    openEditModule(mod) {

      this.moduleMode = 'edit';

      this.moduleForm = {

        id: mod.id,

        judul_modul: mod.judul_modul,

        deskripsi: mod.deskripsi || '',

        urutan: mod.urutan,

        actionUrl: '/admin/elearning/modules/' + mod.id + '/update'

      };

      this.showModuleModal = true;

    },

    openCreateLesson(moduleId = '') {

      this.lessonMode = 'create';

      this.lessonForm = {

        id: null,

        id_module: moduleId,

        judul: '',

        tipe: 'video',

        konten: '',

        media_url: '',

        durasi_menit: 5,

        urutan: {{ ($course->lessons()->max('urutan') ?? 0) + 1 }},

        is_free: '1',

        actionUrl: '{{ route('admin.elearning.lessons.store', $course->id) }}'

      };

      this.showLessonModal = true;

    },

    openEditLesson(les) {

      this.lessonMode = 'edit';

      this.lessonForm = {

        id: les.id,

        id_module: les.id_module || '',

        judul: les.judul,

        tipe: les.tipe,

        konten: les.konten || '',

        media_url: les.media_url || '',

        durasi_menit: les.durasi_menit,

        urutan: les.urutan,

        is_free: les.is_free ? '1' : '0',

        actionUrl: '/admin/elearning/lessons/' + les.id + '/update'

      };

      this.showLessonModal = true;

    },

    async toggleFree(lessonId) {

      try {

        const res = await fetch('/admin/elearning/lessons/' + lessonId + '/toggle-free', {

          method: 'POST',

          headers: {

            'X-CSRF-TOKEN': '{{ csrf_token() }}',

            'Accept': 'application/json',

            'Content-Type': 'application/json'

          }

        });

        const data = await res.json();

        if (data.success) {

          window.dispatchEvent(new CustomEvent('show-toast', {

            detail: { message: data.message, type: 'success' }

          }));

          setTimeout(() => location.reload(), 600);

        }

      } catch (err) {

        console.error(err);

      }

    }

  }" 

  class="space-y-6"

>



  <!-- Breadcrumb & Top Bar -->

  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

    <div>

      <!-- Breadcrumb Navigation -->
      <nav class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 mb-1.5">
        <a href="{{ route('admin.elearning') }}" class="hover:text-amber-500 transition">Materi E-Learning</a>
        <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
        <span class="truncate max-w-xs">{{ $course->judul }}</span>
        <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
        <span class="text-gray-900 dark:text-white font-bold">Kurikulum</span>
      </nav>

      <h1 class="text-xl sm:text-2xl font-black text-gray-900 dark:text-white tracking-tight">

        Kurikulum

      </h1>

      <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">

        Kelola topik modul dan tahapan pembelajaran.

      </p>

    </div>



    <!-- Action Buttons -->

    <div class="flex items-center gap-2.5">

      <button type="button" @click="openCreateModule()" class="px-3.5 py-2 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-900 dark:bg-amber-950/50 dark:text-amber-300 border border-amber-200/80 dark:border-amber-900/60 font-bold text-xs transition inline-flex items-center gap-1.5 shadow-2xs">

        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path></svg>

        <span>Tambah Modul</span>

      </button>



      <button type="button" @click="openCreateLesson()" class="px-4 py-2 rounded-xl bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs shadow-xs transition inline-flex items-center gap-1.5">

        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path></svg>

        <span>Tambah Tahapan</span>

      </button>

    </div>

  </div>



  <!-- Course Clean Summary Banner -->

  <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">

    <div class="flex items-center gap-3.5 min-w-0">

      <div class="w-12 h-12 rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-700 shrink-0 border border-gray-200 dark:border-gray-600 shadow-2xs flex items-center justify-center">
        @php
          $thumbSrc = $course->thumbnail_url ?: ($course->thumbnail ? asset('storage/' . $course->thumbnail) : null);
        @endphp
        @if($thumbSrc)
          <img onerror="this.onerror=null;this.src=window.SVG_IMAGE_FALLBACK;" src="{{ $thumbSrc }}" alt="{{ $course->judul }}" class="w-full h-full object-cover" />
        @else
          <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>
        @endif
      </div>

      <div class="min-w-0">

        <h2 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white truncate">

          {{ $course->judul }}

        </h2>

        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 flex flex-wrap items-center gap-2">

          <span>{{ $course->kategori }}</span>

          <span class="text-gray-300 dark:text-gray-600">|</span>

          <span>{{ $course->modules->count() }} Modul</span>

          <span class="text-gray-300 dark:text-gray-600">|</span>

          <span>{{ $course->lessons->count() }} Tahapan</span>

          <span class="text-gray-300 dark:text-gray-600">|</span>

          @if($course->is_premium || ($course->harga && $course->harga > 0))

            <span class="font-bold text-amber-600 dark:text-amber-400">

              Rp {{ number_format($course->harga, 0, ',', '.') }}

            </span>

          @else

            <span class="font-bold text-emerald-600 dark:text-emerald-400">

              Gratis

            </span>

          @endif

        </p>

      </div>

    </div>



    <div class="shrink-0 flex items-center gap-3">

      <a href="{{ route('public.elearning.show', $course->slug) }}" target="_blank" class="text-xs font-bold text-gray-900 dark:text-white underline hover:text-amber-600 dark:hover:text-amber-400 transition inline-block">

        Pratinjau Ruang Belajar

      </a>

      <a href="{{ route('admin.elearning.edit', $course->id) }}" title="Edit Informasi Kursus" class="p-1.5 rounded-lg text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 hover:bg-amber-50 dark:hover:bg-amber-950/50 transition inline-flex items-center justify-center">

        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 015.25 6H10" /></svg>

      </a>

    </div>

  </div>



  <!-- Curriculum Topics & Sequential Lessons List -->

  <div class="space-y-5">

    @forelse($course->modules as $modIndex => $module)

      <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 overflow-hidden shadow-xs">

        

        <!-- Module Header -->

        <div class="p-4 sm:p-5 bg-gray-50/70 dark:bg-gray-850/60 border-b border-gray-100 dark:border-gray-700/80 flex flex-wrap items-center justify-between gap-3">

          <div class="flex items-center gap-3 min-w-0">

            <span class="px-2.5 py-1 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold text-xs shrink-0 whitespace-nowrap">

              Modul {{ $module->urutan }}

            </span>

            <div class="min-w-0">

              <h3 class="text-sm font-bold text-gray-900 dark:text-white">

                {{ $module->judul_modul }}

              </h3>

              @if($module->deskripsi)

                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 truncate max-w-xl">{{ $module->deskripsi }}</p>

              @endif

            </div>

          </div>



          <div class="flex items-center gap-1.5">

            <button type="button" @click="openCreateLesson('{{ $module->id }}')" class="px-3 py-1.5 rounded-xl bg-[#222222] hover:bg-amber-400 hover:text-gray-900 text-white font-extrabold text-xs transition inline-flex items-center gap-1.5 shadow-xs">

              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path></svg>

              <span>Tahapan</span>

            </button>

            <button type="button" @click="openEditModule({{ json_encode($module) }})" class="p-1.5 rounded-lg text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 hover:bg-amber-50 dark:hover:bg-amber-950/50 transition" title="Edit Topik">

              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>

            </button>

            <button type="button" 

                    @click="$dispatch('open-confirm-modal', {

                      title: 'Hapus Topik Modul?',

                      message: 'Menghapus modul ini juga akan menghapus seluruh tahapan di dalamnya. Apakah Anda yakin?',

                      buttonText: 'Ya, Hapus Topik',

                      iconType: 'delete',

                      buttonClass: 'bg-red-600 hover:bg-red-700 text-white',

                      actionUrl: '{{ route('admin.elearning.modules.destroy', $module->id) }}',

                      method: 'POST'

                    })"

                    class="p-1.5 rounded-lg text-rose-600 dark:text-rose-400 hover:text-rose-700 dark:hover:text-rose-300 hover:bg-rose-50 dark:hover:bg-rose-950/50 transition" 

                    title="Hapus Topik">

              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>

            </button>

          </div>

        </div>



        <!-- Lessons Under this Module (Clean unboxed list) -->

        <div class="p-2 sm:p-3 space-y-1">

          @forelse($module->lessons as $lesson)

            <div class="px-3 sm:px-4 py-2.5 rounded-xl hover:bg-gray-50/80 dark:hover:bg-gray-750/50 transition flex flex-wrap items-center justify-between gap-3">

              <div class="flex items-center gap-3 min-w-0 flex-1">

                <span class="text-xs font-bold text-gray-400 w-5 text-center shrink-0">

                  {{ $lesson->urutan }}

                </span>



                <!-- Type Icon Badge (Pure Neutral Gray) -->

                <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 flex items-center justify-center shrink-0">

                  @if($lesson->tipe === 'video')

                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.348a1.125 1.125 0 010 1.971l-11.54 6.347a1.125 1.125 0 01-1.667-.985V5.653z" /></svg>

                  @elseif($lesson->tipe === 'reading')

                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>

                  @elseif($lesson->tipe === 'audio')

                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 010 12.728M16.463 8.288a5.25 5.25 0 010 7.424M6.75 8.25l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.01 9.01 0 012.25 12c0-.83.112-1.633.322-2.396C2.806 8.757 3.63 8.25 4.51 8.25H6.75z" /></svg>

                  @else

                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>

                  @endif

                </div>



                <div class="min-w-0">

                  <h4 class="text-xs sm:text-sm font-bold text-gray-800 dark:text-gray-200 truncate">

                    {{ $lesson->judul }}

                  </h4>

                  <p class="text-[11px] text-gray-400 mt-0.5">

                    <span class="capitalize">{{ $lesson->tipe }}</span> • {{ $lesson->durasi_menit }} Menit

                    @if($lesson->media_url || $lesson->media_path)

                      • <span class="text-amber-600 dark:text-amber-400 font-medium">Media Terlampir</span>

                    @endif

                  </p>

                </div>

              </div>



              <!-- Badges & Actions -->

              <div class="flex items-center gap-2">

                <!-- Free/Paid Toggle Button (Pure text, no badge box or colors) -->

                <button type="button" @click="toggleFree({{ $lesson->id }})" class="text-xs font-bold text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition cursor-pointer px-1 py-0.5" title="Klik untuk ubah status akses">

                  {{ $lesson->is_free ? 'Gratis' : 'Berbayar' }}

                </button>



                <button type="button" @click="openEditLesson({{ json_encode($lesson) }})" class="p-1.5 rounded-lg text-amber-600 dark:text-amber-400 hover:text-amber-700 dark:hover:text-amber-300 hover:bg-amber-50 dark:hover:bg-amber-950/50 transition" title="Edit Tahapan">

                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>

                </button>



                <button type="button" 

                        @click="$dispatch('open-confirm-modal', {

                          title: 'Hapus Tahapan Belajar?',

                          message: 'Apakah Anda yakin ingin menghapus tahapan {{ addslashes($lesson->judul) }}?',

                          buttonText: 'Ya, Hapus Tahapan',

                          iconType: 'delete',

                          buttonClass: 'bg-red-600 hover:bg-red-700 text-white',

                          actionUrl: '{{ route('admin.elearning.lessons.destroy', $lesson->id) }}',

                          method: 'POST'

                        })"

                        class="p-1.5 rounded-lg text-rose-600 dark:text-rose-400 hover:text-rose-700 dark:hover:text-rose-300 hover:bg-rose-50 dark:hover:bg-rose-950/50 transition" 

                        title="Hapus Tahapan">

                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>

                </button>

              </div>

            </div>

          @empty

            <div class="py-5 text-center text-xs text-gray-400">

              Belum ada tahapan di topik ini. Klik <strong>+ Tahapan</strong> untuk menambahkan.

            </div>

          @endforelse

        </div>



      </div>

    @empty

      <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-12 text-center space-y-4">

        <h3 class="text-base font-bold text-gray-900 dark:text-white">Kurikulum Masih Kosong</h3>

        <p class="text-xs text-gray-500 dark:text-gray-400 max-w-md mx-auto">

          Mulai dengan membuat Topik Modul, lalu masukkan tahapan materi di dalamnya.

        </p>

        <button type="button" @click="openCreateModule()" class="px-4 py-2 rounded-xl bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs shadow-xs transition">

          Buat Topik Modul Pertama

        </button>

      </div>

    @endforelse

  </div>



  <!-- MODAL: Tambah / Edit Modul -->

  <div x-show="showModuleModal" 

       x-cloak 

       class="fixed inset-0 z-50 flex items-center justify-center p-6 sm:p-8 md:p-10 bg-black/60 backdrop-blur-xs overflow-y-auto"

       @keydown.escape.window="showModuleModal = false">

    <div @click.away="showModuleModal = false" class="bg-white dark:bg-gray-850 rounded-2xl border border-gray-200 dark:border-gray-700 w-full max-w-2xl shadow-2xl flex flex-col max-h-[75vh] overflow-hidden my-auto">

      <!-- Header (Rule #59 Compliant: Centered without Icon, NO X button) -->

      <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 text-center shrink-0">

        <h3 class="text-base font-black text-gray-900 dark:text-white text-center" x-text="moduleMode === 'create' ? 'Tambah Modul Baru' : 'Edit Modul'"></h3>

      </div>



      <form :action="moduleForm.actionUrl" method="POST" class="flex flex-col flex-1 overflow-hidden">

        @csrf

        <div class="p-6 overflow-y-auto space-y-4 flex-1">

          <div>

            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Judul Modul <span class="text-rose-500">*</span></label>

            <input type="text" name="judul_modul" x-model="moduleForm.judul_modul" required placeholder="Contoh: Dasar Pencahayaan & Segitiga Exposure" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-xs font-medium focus:ring-2 focus:ring-amber-500 focus:outline-hidden" />

          </div>



          <div>

            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Deskripsi Modul</label>

            <textarea name="deskripsi" x-model="moduleForm.deskripsi" rows="4" placeholder="Ringkasan pembahasan materi pada modul ini..." class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-xs font-medium focus:ring-2 focus:ring-amber-500 focus:outline-hidden resize-y min-h-[100px]"></textarea>

          </div>



          <div>

            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Urutan Modul</label>

            <input type="number" name="urutan" x-model="moduleForm.urutan" min="1" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-xs font-medium focus:ring-2 focus:ring-amber-500 focus:outline-hidden" />

          </div>

        </div>



        <div class="px-6 py-4 bg-gray-50/50 dark:bg-gray-900/30 flex items-center justify-center gap-3 shrink-0">

          <button type="button" @click="showModuleModal = false" class="px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-bold text-xs hover:bg-gray-50 dark:hover:bg-gray-750 transition">

            Batal

          </button>

          <button type="submit" class="px-5 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs shadow-xs transition">

            <span x-text="moduleMode === 'create' ? 'Simpan Data' : 'Perbaharui Data'"></span>

          </button>

        </div>

      </form>

    </div>

  </div>



  <!-- MODAL: Tambah / Edit Tahapan Belajar -->

  <div x-show="showLessonModal" 

       x-cloak 

       class="fixed inset-0 z-50 flex items-center justify-center p-6 sm:p-8 md:p-10 bg-black/60 backdrop-blur-xs overflow-y-auto"

       @keydown.escape.window="showLessonModal = false">

    <div @click.away="showLessonModal = false" class="bg-white dark:bg-gray-850 rounded-2xl border border-gray-200 dark:border-gray-700 w-full max-w-2xl shadow-2xl flex flex-col max-h-[75vh] overflow-hidden my-auto">

      <!-- Header (Rule #59 Compliant: Centered without Icon, NO X button) -->

      <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 text-center shrink-0 space-y-0.5">

        <h3 class="text-base font-black text-gray-900 dark:text-white text-center" x-text="lessonMode === 'create' ? 'Tambah Tahapan Baru' : 'Edit Tahapan'"></h3>

        <p class="text-xs text-gray-500 dark:text-gray-400 text-center">Pilih tipe media pembelajaran dan tentukan status akses materi.</p>

      </div>



      <form :action="lessonForm.actionUrl" method="POST" enctype="multipart/form-data" class="flex flex-col flex-1 overflow-hidden">

        @csrf



        <div class="p-6 overflow-y-auto space-y-4 flex-1">

          <!-- 1. Pilih Modul (Alpine.js Custom Floating Dropdown) -->

          <div>

            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Pilih Modul <span class="text-rose-500">*</span></label>

            <div class="relative" x-data="{ open: false }">

              <input type="hidden" name="id_module" :value="lessonForm.id_module" required>

              <button type="button" 

                      @click="open = !open" 

                      class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-xs font-medium text-gray-900 dark:text-white flex items-center justify-between text-left focus:ring-2 focus:ring-amber-500 focus:outline-hidden transition cursor-pointer">

                <span x-text="

                  @foreach($course->modules as $mod)

                    lessonForm.id_module == '{{ $mod->id }}' ? 'Modul {{ $mod->urutan }}: {{ addslashes($mod->judul_modul) }}' :

                  @endforeach

                  '-- Pilih Modul --'

                " class="truncate"></span>

                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>

              </button>

              <div x-show="open" 

                   @click.away="open = false" 

                   x-transition 

                   class="absolute z-50 left-0 right-0 mt-1.5 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700/80 py-1 max-h-56 overflow-y-auto" 

                   style="display: none;">

                @foreach($course->modules as $mod)

                  <button type="button" 

                          @click="lessonForm.id_module = '{{ $mod->id }}'; open = false;" 

                          class="w-full px-4 py-2.5 text-xs font-medium text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition flex items-center justify-between text-left cursor-pointer border-b border-gray-50 dark:border-gray-700/40 last:border-0"

                          :class="lessonForm.id_module == '{{ $mod->id }}' ? 'bg-amber-50/70 dark:bg-amber-950/30 text-amber-600 font-bold' : ''">

                    <span class="truncate">Modul {{ $mod->urutan }}: {{ $mod->judul_modul }}</span>

                  </button>

                @endforeach

              </div>

            </div>

          </div>



          <!-- 2. Tipe Materi (Alpine.js Custom Floating Dropdown) -->

          <div>

            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Tipe Materi <span class="text-rose-500">*</span></label>

            <div class="relative" x-data="{ open: false }">

              <input type="hidden" name="tipe" :value="lessonForm.tipe" required>

              <button type="button" 

                      @click="open = !open" 

                      class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-xs font-medium text-gray-900 dark:text-white flex items-center justify-between text-left focus:ring-2 focus:ring-amber-500 focus:outline-hidden transition cursor-pointer">

                <span x-text="lessonForm.tipe === 'video' ? 'Video (YouTube / Link Video)' : (lessonForm.tipe === 'reading' ? 'Reading (Teks Artikel & Panduan)' : (lessonForm.tipe === 'audio' ? 'Audio (Podcast / Penjelasan Suara)' : (lessonForm.tipe === 'foto' ? 'Foto (Galeri Visual / Bedah Karya)' : '-- Pilih Tipe --')))"></span>

                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>

              </button>

              <div x-show="open" 

                   @click.away="open = false" 

                   x-transition 

                   class="absolute z-50 left-0 right-0 mt-1.5 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700/80 py-1 max-h-56 overflow-y-auto" 

                   style="display: none;">

                <button type="button" @click="lessonForm.tipe = 'video'; open = false;" class="w-full px-4 py-2.5 text-xs font-medium text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition flex items-center justify-between text-left cursor-pointer border-b border-gray-50 dark:border-gray-700/40" :class="lessonForm.tipe === 'video' ? 'bg-amber-50/70 dark:bg-amber-950/30 text-amber-600 font-bold' : ''">

                  <span>Video (YouTube / Link Video)</span>

                </button>

                <button type="button" @click="lessonForm.tipe = 'reading'; open = false;" class="w-full px-4 py-2.5 text-xs font-medium text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition flex items-center justify-between text-left cursor-pointer border-b border-gray-50 dark:border-gray-700/40" :class="lessonForm.tipe === 'reading' ? 'bg-amber-50/70 dark:bg-amber-950/30 text-amber-600 font-bold' : ''">

                  <span>Reading (Teks Artikel & Panduan)</span>

                </button>

                <button type="button" @click="lessonForm.tipe = 'audio'; open = false;" class="w-full px-4 py-2.5 text-xs font-medium text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition flex items-center justify-between text-left cursor-pointer border-b border-gray-50 dark:border-gray-700/40" :class="lessonForm.tipe === 'audio' ? 'bg-amber-50/70 dark:bg-amber-950/30 text-amber-600 font-bold' : ''">

                  <span>Audio (Podcast / Penjelasan Suara)</span>

                </button>

                <button type="button" @click="lessonForm.tipe = 'foto'; open = false;" class="w-full px-4 py-2.5 text-xs font-medium text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition flex items-center justify-between text-left cursor-pointer last:border-0" :class="lessonForm.tipe === 'foto' ? 'bg-amber-50/70 dark:bg-amber-950/30 text-amber-600 font-bold' : ''">

                  <span>Foto (Galeri Visual / Bedah Karya)</span>

                </button>

              </div>

            </div>

          </div>



          <!-- 3. Judul Tahapan -->

          <div>

            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Judul Tahapan <span class="text-rose-500">*</span></label>

            <input type="text" name="judul" x-model="lessonForm.judul" required placeholder="Contoh: Mengatur Segitiga Exposure di Kondisi Terik" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-xs font-medium text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:outline-hidden" />

          </div>



          <!-- 4. URL Media / Embed -->

          <div x-show="lessonForm.tipe !== 'reading'">

            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">

              <span x-text="lessonForm.tipe === 'video' ? 'Link Video (YouTube / MP4)' : (lessonForm.tipe === 'audio' ? 'Link Streaming Audio / MP3' : 'URL Gambar / Foto')"></span>

            </label>

            <input type="url" name="media_url" x-model="lessonForm.media_url" placeholder="https://www.youtube.com/watch?v=... atau link file media" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-xs font-medium text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:outline-hidden" />

          </div>



          <!-- 5. File Upload Optional -->

          <div x-show="lessonForm.tipe !== 'reading'">

            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Upload File Media (Maks 50MB)</label>

            <input type="file" name="media_file" class="w-full px-3 py-2 text-xs border border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50 dark:bg-gray-800 file:mr-4 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-amber-500 file:text-white hover:file:bg-amber-600" />

          </div>



          <!-- 6. Konten / Catatan / Transkrip -->

          <div>

            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Isi Materi / Penjelasan</label>

            <textarea name="konten" x-model="lessonForm.konten" rows="8" placeholder="Tuliskan materi pembelajaran, langkah-langkah, atau transkrip penjelasan di sini..." class="w-full px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-xs font-medium text-gray-900 dark:text-white leading-relaxed focus:ring-2 focus:ring-amber-500 focus:outline-hidden resize-y min-h-[190px]"></textarea>

          </div>



          <!-- 7. Akses Tahapan (Alpine.js Custom Floating Dropdown - Moved to Durasi's former position) -->

          <div>

            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Akses Tahapan</label>

            <div class="relative" x-data="{ open: false }">

              <input type="hidden" name="is_free" :value="lessonForm.is_free">

              <button type="button" 

                      @click="open = !open" 

                      class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-xs font-medium text-gray-900 dark:text-white flex items-center justify-between text-left focus:ring-2 focus:ring-amber-500 focus:outline-hidden transition cursor-pointer">

                <span x-text="lessonForm.is_free == '1' || lessonForm.is_free === true ? 'Gratis (Free Preview)' : 'Berbayar (Locked Paywall)'"></span>

                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>

              </button>

              <div x-show="open" 

                   @click.away="open = false" 

                   x-transition 

                   class="absolute z-50 left-0 right-0 mt-1.5 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700/80 py-1 max-h-56 overflow-y-auto" 

                   style="display: none;">

                <button type="button" 

                        @click="lessonForm.is_free = '1'; open = false;" 

                        class="w-full px-4 py-2.5 text-xs font-medium text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition flex items-center justify-between text-left cursor-pointer border-b border-gray-50 dark:border-gray-700/40"

                        :class="(lessonForm.is_free == '1' || lessonForm.is_free === true) ? 'bg-amber-50/70 dark:bg-amber-950/30 text-amber-600 font-bold' : ''">

                  <span>Gratis (Free Preview)</span>

                </button>

                <button type="button" 

                        @click="lessonForm.is_free = '0'; open = false;" 

                        class="w-full px-4 py-2.5 text-xs font-medium text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 transition flex items-center justify-between text-left cursor-pointer last:border-0"

                        :class="(lessonForm.is_free == '0' || lessonForm.is_free === false) ? 'bg-amber-50/70 dark:bg-amber-950/30 text-amber-600 font-bold' : ''">

                  <span>Berbayar (Locked Paywall)</span>

                </button>

              </div>

            </div>

          </div>



          <!-- 8. Durasi (Menit) -->

          <div>

            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Durasi (Menit)</label>

            <input type="number" name="durasi_menit" x-model="lessonForm.durasi_menit" min="1" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-xs font-medium text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:outline-hidden" />

          </div>



          <!-- 9. Urutan Tahapan -->

          <div>

            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">Urutan Tahapan</label>

            <input type="number" name="urutan" x-model="lessonForm.urutan" min="1" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-xs font-medium text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:outline-hidden" />

          </div>

        </div>



        <!-- Footer -->

        <div class="px-6 py-4 bg-gray-50/50 dark:bg-gray-900/30 flex items-center justify-center gap-3 shrink-0">

          <button type="button" @click="showLessonModal = false" class="px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-bold text-xs hover:bg-gray-50 dark:hover:bg-gray-750 transition">

            Batal

          </button>

          <button type="submit" class="px-5 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-500 text-gray-900 font-extrabold text-xs shadow-xs transition">

            <span x-text="lessonMode === 'create' ? 'Simpan Data' : 'Perbaharui Data'"></span>

          </button>

        </div>

      </form>

    </div>

  </div>



</div>

@endsection

