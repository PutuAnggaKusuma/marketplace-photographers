@if ($paginator->total() > 0)
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 p-4 text-xs font-bold text-gray-500 dark:text-gray-400">
        
        <!-- Left: Showing page [ X ] of Y (Compact Proportional Box) -->
        <div class="flex items-center gap-2">
            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Showing page</span>
            <span class="px-2.5 py-1 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white font-extrabold text-xs shadow-xs">
                {{ $paginator->currentPage() }}
            </span>
            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">of {{ $paginator->lastPage() }}</span>
        </div>

        <!-- Center: Pagination Controls (<< < 1 2 3 ... 8 9 10 > >>) (Compact 32px Buttons) -->
        <div class="flex items-center gap-1.5 flex-wrap justify-center">
            
            <!-- First Page (<<) -->
            @if ($paginator->onFirstPage())
                <span class="w-8 h-8 rounded-lg border border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50 text-gray-300 dark:text-gray-600 flex items-center justify-center cursor-not-allowed">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path></svg>
                </span>
            @else
                <a @click.prevent="loadAjaxPage('{{ $paginator->url(1) }}')" href="{{ $paginator->url(1) }}" data-ajax-page class="w-8 h-8 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:border-amber-500 hover:text-amber-600 shadow-xs transition flex items-center justify-center">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path></svg>
                </a>
            @endif

            <!-- Previous Page (<) -->
            @if ($paginator->onFirstPage())
                <span class="w-8 h-8 rounded-lg border border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50 text-gray-300 dark:text-gray-600 flex items-center justify-center cursor-not-allowed">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
                </span>
            @else
                <a @click.prevent="loadAjaxPage('{{ $paginator->previousPageUrl() }}')" href="{{ $paginator->previousPageUrl() }}" data-ajax-page class="w-8 h-8 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:border-amber-500 hover:text-amber-600 shadow-xs transition flex items-center justify-center">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
                </a>
            @endif

            <!-- Page Number Links (Compact 32px) -->
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="w-6 text-center text-gray-400 font-bold text-xs">...</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="w-8 h-8 rounded-lg bg-amber-400 text-gray-900 font-black text-xs flex items-center justify-center shadow-xs">
                                {{ $page }}
                            </span>
                        @else
                            <a @click.prevent="loadAjaxPage('{{ $url }}')" href="{{ $url }}" data-ajax-page class="w-8 h-8 rounded-lg border border-transparent text-gray-700 dark:text-gray-300 hover:border-gray-200 dark:hover:border-gray-700 hover:bg-white dark:hover:bg-gray-800 text-xs font-bold flex items-center justify-center transition">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            <!-- Next Page (>) -->
            @if ($paginator->hasMorePages())
                <a @click.prevent="loadAjaxPage('{{ $paginator->nextPageUrl() }}')" href="{{ $paginator->nextPageUrl() }}" data-ajax-page class="w-8 h-8 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:border-amber-500 hover:text-amber-600 shadow-xs transition flex items-center justify-center">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                </a>
            @else
                <span class="w-8 h-8 rounded-lg border border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50 text-gray-300 dark:text-gray-600 flex items-center justify-center cursor-not-allowed">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                </span>
            @endif

            <!-- Last Page (>>) -->
            @if ($paginator->hasMorePages())
                <a @click.prevent="loadAjaxPage('{{ $paginator->url($paginator->lastPage()) }}')" href="{{ $paginator->url($paginator->lastPage()) }}" data-ajax-page class="w-8 h-8 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:border-amber-500 hover:text-amber-600 shadow-xs transition flex items-center justify-center">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7m-8 0l7-7-7-7"></path></svg>
                </a>
            @else
                <span class="w-8 h-8 rounded-lg border border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50 text-gray-300 dark:text-gray-600 flex items-center justify-center cursor-not-allowed">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7m-8 0l7-7-7-7"></path></svg>
                </span>
            @endif

        </div>

        <!-- Right: Rows per page [ 10 | 20 | 50 | 100 v ] (Compact 28px Dropdown) -->
        <div class="flex items-center gap-2" x-data="{ open: false }">
            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Rows per page</span>
            <div class="relative">
                <button @click="open = !open" type="button" class="px-2.5 py-1 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white font-extrabold text-xs flex items-center gap-1.5 shadow-xs hover:border-amber-500 transition">
                    <span>{{ $paginator->perPage() }}</span>
                    <svg class="w-3.5 h-3.5 text-gray-400 transition-transform duration-200" :class="open ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                </button>

                <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 bottom-full mb-1.5 w-20 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700/80 py-1 overflow-hidden z-50">
                    @foreach([10, 20, 50, 100] as $size)
                        @php
                            $targetUrl = request()->fullUrlWithQuery(['per_page' => $size, 'page' => 1]);
                        @endphp
                        <a @click.prevent="loadAjaxPage('{{ $targetUrl }}'); open = false" data-ajax-page 
                           href="{{ $targetUrl }}" 
                           class="block px-2.5 py-1.5 text-center text-xs font-bold {{ $paginator->perPage() == $size ? 'bg-amber-400 text-gray-900 font-black' : 'text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600' }} transition">
                            {{ $size }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
@endif