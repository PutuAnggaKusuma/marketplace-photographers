@if ($paginator->total() > 0)
    <div class="flex flex-col md:flex-row items-center justify-between gap-5 py-2 text-xs sm:text-sm font-bold text-gray-600 dark:text-gray-300">
        
        <!-- Left: Showing page [ X ] of Y (Individual Boxed Current Page - Enlarged) -->
        <div class="flex items-center gap-2.5">
            <span class="text-xs sm:text-sm font-bold">Showing page</span>
            <span class="px-4 py-2 sm:px-4.5 sm:py-2 rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white font-black text-xs sm:text-sm shadow-xs">
                {{ $paginator->currentPage() }}
            </span>
            <span class="text-xs sm:text-sm font-bold">of {{ $paginator->lastPage() }}</span>
        </div>

        <!-- Center: Pagination Controls (<< < 1 2 3 ... 8 9 10 > >>) (Enlarged Proportional Buttons) -->
        <div class="flex items-center gap-2 flex-wrap justify-center">
            
            <!-- First Page (<<) -->
            @if ($paginator->onFirstPage())
                <span class="p-2.5 sm:p-3 rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 text-gray-300 dark:text-gray-600 cursor-not-allowed">
                    <svg class="w-4.5 h-4.5 sm:w-5 sm:h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path></svg>
                </span>
            @else
                <a @click.prevent="typeof loadAjaxPage === 'function' ? loadAjaxPage('{{ $paginator->url(1) }}') : window.location.href='{{ $paginator->url(1) }}'" href="{{ $paginator->url(1) }}" class="p-2.5 sm:p-3 rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:border-amber-500 hover:text-amber-600 shadow-xs transition">
                    <svg class="w-4.5 h-4.5 sm:w-5 sm:h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path></svg>
                </a>
            @endif

            <!-- Previous Page (<) -->
            @if ($paginator->onFirstPage())
                <span class="p-2.5 sm:p-3 rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 text-gray-300 dark:text-gray-600 cursor-not-allowed">
                    <svg class="w-4.5 h-4.5 sm:w-5 sm:h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
                </span>
            @else
                <a @click.prevent="typeof loadAjaxPage === 'function' ? loadAjaxPage('{{ $paginator->previousPageUrl() }}') : window.location.href='{{ $paginator->previousPageUrl() }}'" href="{{ $paginator->previousPageUrl() }}" class="p-2.5 sm:p-3 rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:border-amber-500 hover:text-amber-600 shadow-xs transition">
                    <svg class="w-4.5 h-4.5 sm:w-5 sm:h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
                </a>
            @endif

            <!-- Page Number Links (LensMatch Standard: Active Page bg-amber-400 text-gray-900 font-black) -->
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-2.5 py-1 text-gray-400 font-bold text-xs sm:text-sm">...</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="px-4 py-2.5 sm:px-4.5 sm:py-2.5 rounded-2xl bg-amber-400 text-gray-900 font-black text-xs sm:text-sm shadow-xs">
                                {{ $page }}
                            </span>
                        @else
                            <a @click.prevent="typeof loadAjaxPage === 'function' ? loadAjaxPage('{{ $url }}') : window.location.href='{{ $url }}'" href="{{ $url }}" class="px-4 py-2.5 sm:px-4.5 sm:py-2.5 rounded-2xl border border-transparent text-gray-700 dark:text-gray-300 hover:border-gray-200 dark:hover:border-gray-700 hover:bg-white dark:hover:bg-gray-800 text-xs sm:text-sm font-bold transition">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            <!-- Next Page (>) -->
            @if ($paginator->hasMorePages())
                <a @click.prevent="typeof loadAjaxPage === 'function' ? loadAjaxPage('{{ $paginator->nextPageUrl() }}') : window.location.href='{{ $paginator->nextPageUrl() }}'" href="{{ $paginator->nextPageUrl() }}" class="p-2.5 sm:p-3 rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:border-amber-500 hover:text-amber-600 shadow-xs transition">
                    <svg class="w-4.5 h-4.5 sm:w-5 sm:h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                </a>
            @else
                <span class="p-2.5 sm:p-3 rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 text-gray-300 dark:text-gray-600 cursor-not-allowed">
                    <svg class="w-4.5 h-4.5 sm:w-5 sm:h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                </span>
            @endif

            <!-- Last Page (>>) -->
            @if ($paginator->hasMorePages())
                <a @click.prevent="typeof loadAjaxPage === 'function' ? loadAjaxPage('{{ $paginator->url($paginator->lastPage()) }}') : window.location.href='{{ $paginator->url($paginator->lastPage()) }}'" href="{{ $paginator->url($paginator->lastPage()) }}" class="p-2.5 sm:p-3 rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:border-amber-500 hover:text-amber-600 shadow-xs transition">
                    <svg class="w-4.5 h-4.5 sm:w-5 sm:h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7m-8 0l7-7-7-7"></path></svg>
                </a>
            @else
                <span class="p-2.5 sm:p-3 rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 text-gray-300 dark:text-gray-600 cursor-not-allowed">
                    <svg class="w-4.5 h-4.5 sm:w-5 sm:h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 5l7 7-7 7m-8 0l7-7-7-7"></path></svg>
                </span>
            @endif

        </div>

        <!-- Right: Rows per page [ 10 | 20 | 50 | 100 v ] (Enlarged Proportional Dropdown) -->
        <div class="flex items-center gap-2.5" x-data="{ open: false }">
            <span class="text-xs sm:text-sm font-bold">Rows per page</span>
            <div class="relative">
                <button @click="open = !open" type="button" class="px-4 py-2 sm:px-4.5 sm:py-2 rounded-2xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white font-black text-xs sm:text-sm flex items-center gap-2 shadow-xs hover:border-amber-500 transition">
                    <span>{{ $paginator->perPage() }}</span>
                    <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="open ? 'rotate-180 text-amber-500' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                </button>

                <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 bottom-full mb-2 w-24 bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700/80 py-1.5 overflow-hidden z-50">
                    @foreach([10, 20, 50, 100] as $size)
                        @php
                            $targetUrl = request()->fullUrlWithQuery(['per_page' => $size, 'page' => 1]);
                        @endphp
                        <a @click.prevent="typeof loadAjaxPage === 'function' ? loadAjaxPage('{{ $targetUrl }}') : window.location.href='{{ $targetUrl }}'; open = false" 
                           href="{{ $targetUrl }}" 
                           class="block px-3.5 py-2 text-center text-xs sm:text-sm font-black {{ $paginator->perPage() == $size ? 'bg-amber-400 text-gray-900' : 'text-gray-700 dark:text-gray-200 hover:bg-amber-50 dark:hover:bg-amber-950/40 hover:text-amber-600' }} transition">
                            {{ $size }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

    </div>
@endif