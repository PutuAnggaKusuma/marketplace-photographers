{{--
  x-time-dropdown component (LensMatch UI Standard — Rule #46 & Rule #105)

  Menggantikan <input type="time"> bawaan browser agar UI trigger & menu konsisten
  dengan standar Custom Alpine.js Floating Dropdown LensMatch (grid 2 kolom Jam | Menit 00-59 tanpa garis divider).
--}}
@props([
  'model',
  'name'            => null,
  'placeholder'     => 'Pilih jam',
  'error'           => 'false',
  'align'           => 'left',
  'triggerClass'    => 'w-full px-3.5 py-2 rounded-xl border bg-white dark:bg-gray-900 text-xs flex items-center justify-between gap-2 shadow-2xs outline-none transition cursor-pointer',
  'menuClass'       => 'w-[224px] sm:w-[240px]',
  'dataAttr'        => null,
  'triggerDataAttr' => null,
  'triggerRef'      => null,
  'onSelect'        => '',
])

@php
  $hours = array_map(fn($h) => sprintf('%02d', $h), range(0, 23));
  $minutes = array_map(fn($m) => sprintf('%02d', $m), range(0, 59));
  $alignClass = match ($align) {
      'right' => 'right-0',
      'md-right' => 'left-0 md:left-auto md:right-0',
      default => 'left-0',
  };
@endphp

<div @if($triggerRef) x-ref="{{ $triggerRef }}" @endif class="relative">
  <div x-data="{
         open: false,
         pickedMin: false,
         tempHour: '',
         tempMin: '',
         syncFromModel() {
           const v = String({{ $model }} || '');
           if (v.includes(':')) {
             const parts = v.split(':');
             this.tempHour = parts[0].padStart(2, '0').slice(0, 2);
             this.tempMin = parts[1].padStart(2, '0').slice(0, 2);
           } else {
             this.tempHour = '';
             this.tempMin = '';
           }
         },
         scrollToTarget(box, attr, value) {
           if (!box) return;
           const btn = Array.from(box.querySelectorAll('[' + attr + ']')).find(el => el.getAttribute(attr) === value);
           if (btn) {
             box.scrollTop = Math.max(0, btn.offsetTop - (box.clientHeight / 2) + (btn.clientHeight / 2));
           }
         },
         toggleOpen() {
           this.open = !this.open;
           if (this.open) {
             this.pickedMin = false;
             this.syncFromModel();
             this.$nextTick(() => requestAnimationFrame(() => {
               this.scrollToTarget(this.$refs.hourMenu, 'data-hour', this.tempHour || '08');
               this.scrollToTarget(this.$refs.minuteMenu, 'data-minute', this.tempMin || '00');
             }));
           }
         },
         pickHour(h) {
           this.tempHour = h;
           if (!this.tempMin) {
             this.tempMin = '00';
           }
           {{ $model }} = this.tempHour + ':' + this.tempMin;
           @if($onSelect) {{ rtrim(trim($onSelect), ';') }}; @endif
           if (this.pickedMin) {
             this.open = false;
           }
         },
         pickMin(m) {
           this.tempMin = m;
           this.pickedMin = true;
           if (this.tempHour) {
             {{ $model }} = this.tempHour + ':' + this.tempMin;
             @if($onSelect) {{ rtrim(trim($onSelect), ';') }}; @endif
             this.open = false;
           }
         }
       }"
       @keydown.escape="open = false"
       class="relative">
    @if($name)
      <input type="hidden"
             name="{{ $name }}"
             :value="{{ $model }}"
             @if($dataAttr) {{ $dataAttr }} @endif>
    @endif

    <button type="button"
            data-time-trigger
            @if($triggerDataAttr) {{ $triggerDataAttr }} @endif
            @click="toggleOpen()"
            :aria-expanded="open"
            :class="[
              ({{ $error }})
                ? 'border-red-400 focus:ring-2 focus:ring-red-400 focus:border-red-400'
                : (open
                    ? 'border-amber-400 dark:border-amber-500 ring-2 ring-amber-400/30'
                    : 'border-gray-200 dark:border-gray-700 hover:border-amber-400 dark:hover:border-amber-500 focus:ring-2 focus:ring-amber-400'),
              !{{ $model }}
                ? 'text-gray-400 dark:text-gray-500 font-medium'
                : 'text-gray-800 dark:text-white font-bold'
            ]"
            class="{{ $triggerClass }}">
      <span x-text="{{ $model }} || '{{ $placeholder }}'" class="tabular-nums truncate"></span>
      <svg class="w-3.5 h-3.5 text-gray-400 shrink-0 transition-transform duration-200"
           :class="open ? 'rotate-180 text-amber-500' : ''"
           fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
      </svg>
    </button>

    <div x-show="open"
         @click.outside="open = false"
         x-transition
         style="display: none;"
         class="absolute z-50 {{ $alignClass }} mt-1.5 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700/80 overflow-hidden {{ $menuClass }}">
      <div class="p-2.5 grid grid-cols-2 gap-2">
        <!-- Kolom Jam (00 - 23) -->
        <div class="flex flex-col min-w-0">
          <div class="px-2 pb-1.5 text-center text-[10px] font-extrabold uppercase tracking-wider text-gray-400 dark:text-gray-500">
            Jam
          </div>
          <div x-ref="hourMenu" class="relative max-h-56 overflow-y-auto custom-scrollbar space-y-0.5 pr-1">
            @foreach($hours as $h)
              <button type="button"
                      data-hour="{{ $h }}"
                      @click="pickHour('{{ $h }}')"
                      :class="tempHour === '{{ $h }}' ? 'bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 font-extrabold' : 'text-gray-700 dark:text-gray-200 font-bold'"
                      class="w-full px-3 py-1.5 rounded-lg text-xs tabular-nums text-center hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 dark:hover:text-amber-400 transition cursor-pointer">
                {{ $h }}
              </button>
            @endforeach
          </div>
        </div>

        <!-- Kolom Menit (00 - 59) -->
        <div class="flex flex-col min-w-0">
          <div class="px-2 pb-1.5 text-center text-[10px] font-extrabold uppercase tracking-wider text-gray-400 dark:text-gray-500">
            Menit
          </div>
          <div x-ref="minuteMenu" class="relative max-h-56 overflow-y-auto custom-scrollbar space-y-0.5 pr-1">
            @foreach($minutes as $m)
              <button type="button"
                      data-minute="{{ $m }}"
                      @click="pickMin('{{ $m }}')"
                      :class="tempMin === '{{ $m }}' ? 'bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 font-extrabold' : 'text-gray-700 dark:text-gray-200 font-bold'"
                      class="w-full px-3 py-1.5 rounded-lg text-xs tabular-nums text-center hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600 dark:hover:text-amber-400 transition cursor-pointer">
                {{ $m }}
              </button>
            @endforeach
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
