@extends('layouts.app')



@section('title', 'Pesan & Diskusi Fotografer — LensMatch')



@section('content')

<div class="flex-1 flex w-full h-full min-h-0 bg-white dark:bg-gray-800 overflow-hidden relative select-none"

     x-data="clientChatApp(

         {{ Illuminate\Support\Js::from($formattedThreads) }},

         {{ $activeThread ? $activeThread->id : 'null' }},

         {{ Illuminate\Support\Js::from($formattedMessages) }},

         {{ Illuminate\Support\Js::from($activeThread ? ($activeThread->status_user ?? ['is_online' => false, 'label' => 'Aktif 7+ hari lalu', 'color' => 'text-gray-400', 'dot_color' => 'bg-gray-400']) : ['is_online' => false, 'label' => 'Aktif 7+ hari lalu', 'color' => 'text-gray-400', 'dot_color' => 'bg-gray-400']) }}

     )">

    

    <!-- Left Panel: Thread List (Collapsible Mini-Sidebar) -->

    <div class="border-r border-gray-200 dark:border-gray-700/80 flex flex-col h-full min-h-0 bg-white dark:bg-gray-800 overflow-hidden shrink-0 transition-all duration-300 ease-in-out"

         :class="[chatListCollapsed ? 'w-20' : 'w-full sm:w-80 lg:w-96', activeThread ? 'hidden sm:flex' : 'flex']">

        

        <!-- Header with Spacing Between and Outline Collapse Toggle Button -->

        <div class="p-4 border-b border-gray-200 dark:border-gray-700/80 shrink-0 flex items-center gap-2"

             :class="chatListCollapsed ? 'justify-center px-2' : 'justify-between px-4.5'">

            

            <div x-show="!chatListCollapsed" x-transition.opacity class="truncate min-w-0">

                <h2 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-wider">Daftar Chat Fotografer</h2>

                <p class="text-[11px] text-gray-500 mt-0.5 truncate">Pesan langsung dengan fotografer</p>

            </div>



            <!-- Outline Collapse / Expand Toggle Button -->

            <button type="button" 

                    @click="chatListCollapsed = !chatListCollapsed" 

                    :title="chatListCollapsed ? 'Buka Daftar Fotografer Lengkap' : 'Sembunyikan Daftar Fotografer (Fokus Room Chat)'" 

                    class="p-2 rounded-xl text-gray-500 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700 transition active:scale-95 shrink-0 cursor-pointer">

                <!-- Icon Collapse (Single Chevron Kiri) -->

                <svg x-show="!chatListCollapsed" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">

                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />

                </svg>

                <!-- Icon Expand (Single Chevron Kanan) -->

                <svg x-show="chatListCollapsed" class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" style="display: none;">

                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />

                </svg>

            </button>

        </div>



        <!-- Reactive Threads List Stream (SPA Real-Time No Reload) -->

        <div class="flex-1 min-h-0 overflow-y-auto divide-y divide-gray-100 dark:divide-gray-700/60 no-scrollbar">

            <template x-for="t in threads" :key="t.id">

                <button type="button" 

                        @click="switchThread(t.id)" 

                        class="w-full text-left flex items-center gap-3.5 hover:bg-amber-50/60 dark:hover:bg-amber-950/30 transition relative cursor-pointer"

                        :class="[

                            chatListCollapsed ? 'p-3 justify-center' : 'p-4',

                            activeThreadId === t.id ? 'bg-amber-50/80 dark:bg-amber-950/40 text-amber-950 dark:text-amber-100' : 'text-gray-700 dark:text-gray-300'

                        ]">

                    

                    <!-- Circular Avatar & Real-Time Online Dot -->

                    <div class="relative shrink-0" :title="chatListCollapsed ? t.name : ''">

                        <div class="w-11 h-11 rounded-full bg-amber-500 flex items-center justify-center text-white font-black text-sm shadow-xs overflow-hidden"

                             :class="activeThreadId === t.id ? 'ring-2 ring-amber-500 ring-offset-2 dark:ring-offset-gray-800' : ''">

                            <template x-if="t.foto">

                                <img :src="t.foto" class="w-full h-full object-cover" x-on:error="$el.style.display='none'; if ($el.nextElementSibling) $el.nextElementSibling.style.display='flex'">

                            </template>

                            <div class="w-full h-full flex items-center justify-center" :style="t.foto ? 'display: none;' : ''">

                                <span x-text="t.initial"></span>

                            </div>

                        </div>

                        

                        <!-- Unread badge when collapsed -->

                        <template x-if="chatListCollapsed && t.unread_count > 0">

                            <span class="absolute -top-1 -right-1 px-1.5 py-0.5 rounded-md text-[10px] font-black bg-rose-500 text-white animate-pulse" x-text="t.unread_count"></span>

                        </template>

                    </div>



                    <!-- Photographer Info (Hidden when mini-sidebar is collapsed) -->

                    <div x-show="!chatListCollapsed" x-transition.opacity class="flex-1 min-w-0">

                        <div class="flex items-center justify-between gap-2">

                            <h3 class="text-xs font-black text-gray-900 dark:text-white truncate" x-text="t.name"></h3>

                            <span class="text-[10px] text-gray-400 shrink-0" x-text="formatLocalTime(t.last_message_iso, t.last_message_time)"></span>

                        </div>

                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-1 font-medium" x-text="t.last_message"></p>

                    </div>



                    <!-- Unread Count Badge (Expanded Mode) -->

                    <template x-if="!chatListCollapsed && t.unread_count > 0">

                        <span class="ml-1 shrink-0 px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-500 text-white" x-text="t.unread_count"></span>

                    </template>

                </button>

            </template>



            <!-- Empty Thread State -->

            <template x-if="threads.length === 0">

                <div class="p-8 text-center space-y-2">

                    <p class="text-xs text-gray-400">Belum ada percakapan dengan fotografer.</p>

                    <a href="{{ route('public.photographers.katalog') }}" class="inline-block text-xs text-amber-500 font-bold hover:underline">Jelajahi Fotografer</a>

                </div>

            </template>

        </div>

    </div>



    <!-- Right Panel: Main Chat Box -->

    <div class="flex-1 flex flex-col h-full min-h-0 overflow-hidden bg-white dark:bg-gray-800"

         :class="!activeThread ? 'hidden sm:flex' : 'flex'">

        

        <!-- When Active Thread Selected -->

        <div x-show="activeThread" 

             style="{{ !$activeThread ? 'display: none;' : '' }}"

             class="flex-1 flex flex-col h-full min-h-0 overflow-hidden">

            

            <!-- Active Photographer Header -->

            <div class="p-4 sm:px-6 border-b border-gray-200 dark:border-gray-700/80 flex items-center justify-between bg-white dark:bg-gray-800 shrink-0 z-20">

                <div class="flex items-center gap-3">

                    <button type="button" 

                            @click="activeThread = null; activeThreadId = null; window.history.pushState({}, '', window.location.pathname)" 

                            class="sm:hidden p-2 -ml-2 text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white rounded-xl transition cursor-pointer shrink-0"

                            title="Kembali ke Daftar Chat">

                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>

                    </button>

                    <!-- Avatar with Hover Bubble Tooltip (Rule #65 Profile Detail Standard) -->

                    <div class="relative group shrink-0">

                        <a :href="activeThread && activeThread.photographer_id ? ('/fotografer/' + activeThread.photographer_id) : '#'" 

                           :class="activeThread && activeThread.photographer_id ? 'cursor-pointer' : 'cursor-default'"

                           class="block focus:outline-none">

                            <div class="w-10 h-10 rounded-full bg-amber-500 flex items-center justify-center text-white font-black text-xs shadow-xs overflow-hidden shrink-0 transition-transform duration-200 group-hover:scale-105 group-hover:ring-2 group-hover:ring-amber-400">

                                <template x-if="activeThread && activeThread.foto">

                                    <img :src="activeThread.foto" class="w-full h-full object-cover" x-on:error="$el.style.display='none'; if ($el.nextElementSibling) $el.nextElementSibling.style.display='flex'">

                                </template>

                                <div class="w-full h-full flex items-center justify-center" :style="(activeThread && activeThread.foto) ? 'display: none;' : ''">

                                    <span x-text="activeThread ? activeThread.initial : ''"></span>

                                </div>

                            </div>

                        </a>



                        <!-- Floating Chat-Bubble Tooltip on Hover -->

                        <template x-if="activeThread && activeThread.photographer_id">

                            <a :href="'/fotografer/' + activeThread.photographer_id"

                               class="absolute left-1/2 -bottom-9 -translate-x-1/2 opacity-0 -translate-y-1 group-hover:opacity-100 group-hover:translate-y-0 pointer-events-none group-hover:pointer-events-auto transition-all duration-200 z-30 whitespace-nowrap">

                                <div class="relative px-2.5 py-1 rounded-lg bg-gray-900/95 dark:bg-white text-white dark:text-gray-900 text-[10px] font-extrabold tracking-wide shadow-xl flex items-center gap-1.5 border border-white/10 dark:border-gray-200">

                                    <!-- Tiny bubble tail pointing up to avatar -->

                                    <div class="absolute -top-1 left-1/2 -translate-x-1/2 w-2 h-2 bg-gray-900/95 dark:bg-white rotate-45 border-t border-l border-white/10 dark:border-gray-200"></div>

                                    <span>Detail Profil</span>

                                </div>

                            </a>

                        </template>

                    </div>

                    <div>

                        <h3 class="text-sm font-black text-gray-900 dark:text-white leading-tight" x-text="activeThread ? activeThread.name : ''"></h3>

                        

                        <!-- Typing Indicator or Online Status -->

                        <div class="mt-0.5">

                            <template x-if="otherIsTyping">

                                <div class="flex items-center gap-1.5 text-[11px] text-amber-600 dark:text-amber-400 font-extrabold animate-pulse">

                                    <span>sedang mengetik</span>

                                    <span class="inline-flex gap-0.5">

                                        <span class="w-1 h-1 bg-amber-500 rounded-full animate-bounce" style="animation-delay: 0ms"></span>

                                        <span class="w-1 h-1 bg-amber-500 rounded-full animate-bounce" style="animation-delay: 150ms"></span>

                                        <span class="w-1 h-1 bg-amber-500 rounded-full animate-bounce" style="animation-delay: 300ms"></span>

                                    </span>

                                </div>

                            </template>

                            <template x-if="!otherIsTyping">

                                <p class="text-[11px] font-semibold" :class="userStatus.color">

                                    <span x-text="userStatus.label"></span>

                                </p>

                            </template>

                        </div>

                    </div>

                </div>



                <div class="flex items-center gap-2">

                    <!-- Tombol Pencarian Pesan dalam Chat -->

                    <button type="button" 

                            @click="toggleSearchBar()" 

                            title="Cari pesan"

                            :class="searchBarOpen ? 'text-amber-500 bg-amber-50 dark:bg-amber-950/40' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700'"

                            class="p-2 rounded-xl transition cursor-pointer">

                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>

                    </button>

                </div>

            </div>



            <!-- In-Chat Search Bar -->

            <div x-show="searchBarOpen" 

                 x-cloak 

                 x-transition:enter="transition ease-out duration-150"

                 x-transition:enter-start="opacity-0 -translate-y-2"

                 x-transition:enter-end="opacity-100 translate-y-0"

                 x-transition:leave="transition ease-in duration-100"

                 x-transition:leave-start="opacity-100 translate-y-0"

                 x-transition:leave-end="opacity-0 -translate-y-2"

                 class="px-4 py-2.5 bg-gray-50 dark:bg-gray-850 border-b border-gray-200 dark:border-gray-700/80 flex items-center gap-3 text-xs z-20">

                <div class="relative flex-1">

                    <input type="text" 

                           x-ref="searchInput"

                           x-model="searchKeyword" 

                           @input="performSearch()"

                           @keydown.enter.prevent="nextSearchResult()"

                           @keydown.escape="closeSearchBar()"

                           placeholder="Cari pesan di percakapan ini..." 

                           class="w-full pl-4 pr-10 py-2 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-xs text-gray-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500">

                    <svg x-show="!searchKeyword" class="w-4 h-4 text-gray-400 absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>

                    <button x-show="searchKeyword" @click="searchKeyword = ''; performSearch()" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 cursor-pointer">

                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>

                    </button>

                </div>

                

                <!-- Match Navigation Buttons -->

                <div class="flex items-center gap-1.5 shrink-0 select-none text-[11px] text-gray-500 dark:text-gray-400">

                    <span x-text="searchResultsText"></span>

                    <button type="button" 

                            @click="prevSearchResult()" 

                            :disabled="searchResults.length === 0"

                            title="Sebelumnya"

                            class="p-1 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 disabled:opacity-40 transition cursor-pointer">

                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>

                    </button>

                    <button type="button" 

                            @click="nextSearchResult()" 

                            :disabled="searchResults.length === 0"

                            title="Berikutnya"

                            class="p-1 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 disabled:opacity-40 transition cursor-pointer">

                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>

                    </button>

                </div>



                <!-- Close Search -->

                <button type="button" @click="closeSearchBar()" class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition cursor-pointer">

                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>

                </button>

            </div>



            <!-- Messages Stream Area with Subtle WhatsApp Doodle Wallpaper -->

            <div class="flex-1 min-h-0 relative overflow-hidden bg-[#efeae2] dark:bg-[#0b141a]">

                <!-- Light Mode Doodle Texture Overlay -->

                <div class="absolute inset-0 bg-repeat pointer-events-none opacity-35 dark:hidden" style="background-image: url('{{ asset('images/chat-bg-pattern-light.png') }}'); background-size: 360px;"></div>

                <!-- Dark Mode Doodle Texture Overlay -->

                <div class="absolute inset-0 bg-repeat pointer-events-none opacity-25 hidden dark:block" style="background-image: url('{{ asset('images/chat-bg-pattern-dark.png') }}'); background-size: 360px;"></div>



                <!-- Messages Scroll Container -->

                <div x-ref="msgContainer" class="relative z-10 h-full p-4 sm:p-6 overflow-y-auto">

                    <div class="min-h-full flex flex-col justify-end space-y-3">

                        <div class="flex-1 min-h-0"></div>

                        <template x-for="m in messages" :key="m.id">

                            <div :class="m.is_me ? 'flex justify-end' : 'flex justify-start'">

                                <div :id="'chat_msg_' + m.id"

                                     x-data="{ openMenu: false }" 

                                     @click.outside="openMenu = false"

                                     :class="[

                                         m.is_me 

                                             ? (m.deleted_for_all ? 'bg-amber-500/75 text-amber-100 rounded-2xl rounded-tr-none shadow-xs' : 'bg-amber-500 text-white rounded-2xl rounded-tr-none shadow-xs') 

                                             : (m.deleted_for_all ? 'bg-gray-100 dark:bg-gray-800/80 text-gray-400 dark:text-gray-400 border border-gray-200 dark:border-gray-700/80 rounded-2xl rounded-tl-none shadow-xs' : 'bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 border border-gray-200 dark:border-gray-700 rounded-2xl rounded-tl-none shadow-xs'),

                                         highlightedMsgId === m.id ? 'ring-4 ring-amber-400/90 ring-offset-2 dark:ring-offset-gray-900 transition-all duration-300' : ''

                                     ]"

                                     class="max-w-md px-4 py-2.5 space-y-1 relative group">

                                    

                                    <!-- WhatsApp Web Style Chevron Dropdown Trigger -->

                                    <template x-if="!m.deleted_for_all && !m.pending">

                                        <button type="button" 

                                                @click.stop="openMenu = !openMenu" 

                                                class="absolute top-1.5 right-1.5 p-1 rounded-full opacity-0 group-hover:opacity-100 focus:opacity-100 transition-opacity cursor-pointer z-10"

                                                :class="m.is_me ? 'text-amber-100 hover:text-white hover:bg-amber-600/50' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700'">

                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" /></svg>

                                        </button>

                                    </template>



                                    <!-- Context Dropdown Menu -->

                                    <div x-show="openMenu" 

                                         x-cloak

                                         x-transition:enter="transition ease-out duration-100"

                                         x-transition:enter-start="transform opacity-0 scale-95"

                                         x-transition:enter-end="transform opacity-100 scale-100"

                                         x-transition:leave="transition ease-in duration-75"

                                         x-transition:leave-start="transform opacity-100 scale-100"

                                         x-transition:leave-end="transform opacity-0 scale-95"

                                         :class="m.is_me ? 'right-0' : 'left-0'"

                                         class="absolute top-7 z-30 w-36 bg-white dark:bg-gray-850 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 py-1 text-xs select-none">

                                        

                                        <!-- Balas (Quote Reply) -->

                                        <button type="button" 

                                                @click="startReplyMessage(m); openMenu = false;"

                                                class="w-full text-left px-3 py-1.5 flex items-center gap-2 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-750 cursor-pointer">

                                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a5 5 0 015 5v3M3 10l6-6m-6 6l6 6"/></svg>

                                            <span>Balas</span>

                                        </button>



                                        <!-- Salin -->

                                        <button type="button" 

                                                @click="copyMessage(m.message); openMenu = false;"

                                                class="w-full text-left px-3 py-1.5 flex items-center gap-2 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-750 cursor-pointer">

                                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>

                                            <span>Salin</span>

                                        </button>



                                        <!-- Edit (Hanya jika teks & masih dalam 15 menit & belum dihapus) -->

                                        <template x-if="m.can_edit && !m.attachment_url">

                                            <button type="button" 

                                                    @click="startEditMessage(m); openMenu = false;"

                                                    class="w-full text-left px-3 py-1.5 flex items-center gap-2 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-750 cursor-pointer">

                                                <svg class="w-3.5 h-3.5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>

                                                <span>Edit</span>

                                            </button>

                                        </template>



                                        <!-- Hapus -->

                                        <button type="button" 

                                                @click="promptDeleteMessage(m); openMenu = false;"

                                                class="w-full text-left px-3 py-1.5 flex items-center gap-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 cursor-pointer">

                                            <svg class="w-3.5 h-3.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>

                                            <span>Hapus</span>

                                        </button>

                                    </div>



                                    <!-- Quoted Reply Box inside Bubble -->

                                    <template x-if="m.reply_to && !m.deleted_for_all">

                                        <div @click="scrollToMessage(m.reply_to.id)" 

                                             class="mb-2 p-2 rounded-xl text-left cursor-pointer transition select-none flex items-start gap-2 border-l-4"

                                             :class="m.is_me ? 'bg-amber-600/40 border-amber-200 text-amber-50' : 'bg-gray-100 dark:bg-gray-750/90 border-amber-500 text-gray-700 dark:text-gray-200'">

                                            <div class="flex-1 min-w-0">

                                                <div class="font-bold text-[10px] leading-tight" 

                                                     :class="m.is_me ? 'text-amber-200' : 'text-amber-600 dark:text-amber-400'"

                                                     x-text="m.reply_to.sender_name"></div>

                                                <div class="text-[11px] truncate opacity-90 leading-tight mt-0.5" x-text="m.reply_to.message || (m.reply_to.attachment_url ? '[Foto]' : '')"></div>

                                            </div>

                                            <template x-if="m.reply_to.attachment_url">

                                                <img :src="m.reply_to.attachment_url" class="w-8 h-8 rounded-lg object-cover shrink-0 ml-1">

                                            </template>

                                        </div>

                                    </template>



                                    <!-- Attachment Photo inside Bubble -->

                                    <template x-if="m.attachment_url && !m.deleted_for_all">

                                        <div class="mb-1 rounded-xl overflow-hidden cursor-pointer group/img relative" @click="openImageModal(m.attachment_url)">

                                            <img :src="m.attachment_url" @load="scrollToBottom()" class="max-h-60 w-full object-cover rounded-xl transition hover:opacity-95" loading="lazy">

                                            <div class="absolute inset-0 bg-black/20 opacity-0 group-hover/img:opacity-100 transition flex items-center justify-center">

                                                <svg class="w-6 h-6 text-white drop-shadow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>

                                            </div>

                                        </div>

                                    </template>



                                    <!-- Message Content Body -->

                                    <template x-if="m.deleted_for_all">

                                        <div class="flex items-center gap-1.5 italic text-xs py-0.5" :class="m.is_me ? 'text-amber-100/90' : 'text-gray-400 dark:text-gray-400'">

                                            <svg class="w-3.5 h-3.5 opacity-80 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>

                                            <span>Pesan ini telah dihapus</span>

                                        </div>

                                    </template>

                                    <!-- Custom Offer Card Component -->

                                    <template x-if="m.custom_offer && !m.deleted_for_all">

                                        <div class="my-1.5 p-3.5 rounded-2xl text-left border shadow-xs space-y-3"

                                             :class="m.is_me ? 'bg-amber-600/40 border-amber-300/50 text-white' : 'bg-amber-50/90 dark:bg-gray-750 border-amber-200 dark:border-gray-700 text-gray-900 dark:text-white'">

                                            

                                            <!-- Header: Badge & Status -->

                                            <div class="flex items-center justify-between gap-2 border-b pb-2"

                                                 :class="m.is_me ? 'border-amber-400/30' : 'border-amber-200/60 dark:border-gray-700'">

                                                <div class="flex items-center gap-1.5">

                                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider bg-amber-400 text-gray-950 shadow-2xs">

                                                        Penawaran Khusus

                                                    </span>

                                                </div>

                                                <div>

                                                    <template x-if="m.custom_offer.status === 'pending'">

                                                        <span class="text-[10px] font-bold text-amber-300 dark:text-amber-400">Menunggu Respon</span>

                                                    </template>

                                                    <template x-if="m.custom_offer.status === 'accepted'">

                                                        <span class="text-[10px] font-black text-emerald-400">Disetujui</span>

                                                    </template>

                                                    <template x-if="m.custom_offer.status === 'rejected'">

                                                        <span class="text-[10px] font-bold text-rose-300">Ditolak</span>

                                                    </template>

                                                </div>

                                            </div>



                                            <!-- Title & Price -->

                                            <div>

                                                <h4 class="text-sm font-black leading-snug" x-text="m.custom_offer.judul_penawaran"></h4>

                                                <div class="text-base font-black mt-1" :class="m.is_me ? 'text-amber-200' : 'text-gray-950 dark:text-amber-400'" x-text="m.custom_offer.formatted_harga"></div>

                                            </div>



                                            <!-- Meta Schedule & Location (2 Columns) -->

                                            <div class="grid grid-cols-2 gap-2 text-[11px] py-1.5 px-2.5 rounded-xl"

                                                 :class="m.is_me ? 'bg-amber-700/40 text-amber-100' : 'bg-white/80 dark:bg-gray-800/80 text-gray-600 dark:text-gray-300 border border-gray-100 dark:border-gray-700/60'">

                                                <div>

                                                    <span class="text-[9px] uppercase tracking-wider opacity-70 block font-semibold">Jadwal Sesi</span>

                                                    <span class="font-bold block truncate" x-text="m.custom_offer.booking_date"></span>

                                                    <span class="text-[10px] block opacity-80" x-text="m.custom_offer.booking_time"></span>

                                                </div>

                                                <div>

                                                    <span class="text-[9px] uppercase tracking-wider opacity-70 block font-semibold">Durasi & Lokasi</span>

                                                    <span class="font-bold block truncate" x-text="m.custom_offer.durasi_jam + ' Jam'"></span>

                                                    <span class="text-[10px] block truncate opacity-80" x-text="m.custom_offer.lokasi"></span>

                                                </div>

                                            </div>



                                            <!-- Description Note -->

                                            <p class="text-xs leading-relaxed opacity-90 italic" x-text="m.custom_offer.deskripsi"></p>



                                            <!-- Action Buttons for Recipient (Client Only) -->

                                            <template x-if="!m.is_me && m.custom_offer.status === 'pending'">

                                                <div class="pt-2 flex items-center gap-2 border-t"

                                                     :class="m.is_me ? 'border-amber-400/30' : 'border-amber-200/60 dark:border-gray-700'">

                                                    <button type="button" 

                                                            @click="rejectOffer(m.custom_offer.id)"

                                                            class="flex-1 py-2 px-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 hover:bg-gray-50 text-gray-700 dark:text-gray-300 font-bold text-xs transition cursor-pointer text-center">

                                                        Tolak

                                                                                <button type="button" 

                                                            @click="acceptOffer(m.custom_offer.id)"

                                                            class="flex-1 py-2 px-3 rounded-xl bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs transition shadow-xs cursor-pointer text-center">

                                                        Setujui & Bayar

                                                                            </div>

                                            </template>



                                            <!-- Accepted Link to Invoice -->

                                            <template x-if="m.custom_offer.status === 'accepted' && m.custom_offer.invoice_url">

                                                <div class="pt-1">

                                                    <a :href="m.custom_offer.invoice_url" 

                                                       class="inline-flex items-center gap-1.5 text-xs font-extrabold underline underline-offset-4"

                                                       :class="m.is_me ? 'text-amber-200 hover:text-white' : 'text-amber-600 dark:text-amber-400 hover:underline'">

                                                        <span>Buka Halaman Invoice & Pembayaran</span><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>

                                                    </a>

                                                </div>

                                            </template>



                                        </div>

                                    </template>



                                    <template x-if="!m.deleted_for_all && m.message && !m.custom_offer">

                                        <p class="text-xs leading-relaxed break-words whitespace-pre-line pr-5" x-text="m.message"></p>

                                    </template>



                                    <!-- WhatsApp Style Bottom-Right Timestamp & Indicators -->

                                    <div class="flex items-center justify-end gap-1 text-[10px] select-none pt-0.5" 

                                         :class="m.is_me ? 'text-amber-100/90' : 'text-gray-400'">

                                        

                                        <!-- Diedit Indicator -->

                                        <template x-if="m.is_edited && !m.deleted_for_all">

                                            <span class="italic text-[9px] mr-0.5 opacity-80 font-normal">diedit</span>

                                        </template>



                                        <span x-text="formatLocalTime(m.created_at || m.time, m.time)"></span>



                                        <!-- WhatsApp Ticks (Only for My Sent Messages) -->

                                        <template x-if="m.is_me && !m.deleted_for_all">

                                            <span class="inline-flex items-center ml-0.5">

                                                <!-- Kondisi 1: Centang 2 Biru (Sudah Dibaca / Read) -->

                                                <template x-if="m.is_read">

                                                    <svg class="w-3.5 h-3 text-sky-200 drop-shadow-xs" viewBox="0 0 16 15" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" title="Dibaca">

                                                        <path d="M1.5 8.5L4.5 11.5L11.5 4.5" />

                                                        <path d="M5.5 8.5L8.5 11.5L15.5 4.5" />

                                                    </svg>

                                                </template>



                                                <!-- Kondisi 2: Centang 2 Kuning/Abu Lembut (Fotografer Online tapi Belum Dibaca) -->

                                                <template x-if="!m.is_read && userStatus.is_online">

                                                    <svg class="w-3.5 h-3 text-amber-200/80" viewBox="0 0 16 15" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" title="Tersampaikan">

                                                        <path d="M1.5 8.5L4.5 11.5L11.5 4.5" />

                                                        <path d="M5.5 8.5L8.5 11.5L15.5 4.5" />

                                                    </svg>

                                                </template>



                                                <!-- Kondisi 3: Centang 1 (Terkirim ke Server, Fotografer Sedang Offline) -->

                                                <template x-if="!m.is_read && !userStatus.is_online">

                                                    <svg class="w-3.5 h-3 text-amber-200/70" viewBox="0 0 16 15" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" title="Terkirim">

                                                        <path d="M2.5 8.5L6 12L13.5 3.5" />

                                                    </svg>

                                                </template>

                                            </span>

                                        </template>

                                    </div>



                                </div>

                            </div>

                        </template>

                    </div>

                </div>

            </div>



            <!-- Input Message Bar, Banners & Photo Attachment -->

            <div class="border-t border-gray-200 dark:border-gray-700/80 bg-white dark:bg-gray-800 shrink-0 z-20">

                

                <!-- 1. Edit Banner -->

                <div x-show="editingMessage" 

                     x-cloak 

                     class="px-4 py-2.5 bg-amber-50 dark:bg-gray-750 border-b border-amber-200 dark:border-gray-700 flex items-center justify-between text-xs transition">

                    <div class="flex items-center gap-2.5 overflow-hidden">

                        <div class="w-1 h-7 bg-amber-500 rounded-full shrink-0"></div>

                        <div class="truncate">

                            <div class="font-bold text-amber-700 dark:text-amber-400 text-[11px] leading-tight">Edit Pesan (Batas 15 menit)</div>

                            <div class="text-gray-600 dark:text-gray-300 truncate text-[11px] italic" x-text="editingMessage ? editingMessage.message : ''"></div>

                        </div>

                    </div>

                    <button type="button" 

                            @click="cancelEdit()" 

                            title="Batal edit"

                            class="p-1 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-200/70 dark:hover:bg-gray-700 cursor-pointer transition">

                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>

                    </button>

                </div>



                <!-- 2. Reply (Quote) Banner -->

                <div x-show="replyingToMessage" 

                     x-cloak 

                     class="px-4 py-2.5 bg-amber-50/80 dark:bg-gray-750 border-b border-amber-200 dark:border-gray-700 flex items-center justify-between text-xs transition">

                    <div class="flex items-center gap-2.5 overflow-hidden">

                        <div class="w-1 h-8 bg-amber-500 rounded-full shrink-0"></div>

                        <div class="truncate">

                            <div class="flex items-center gap-1.5">

                                <span class="font-bold text-amber-700 dark:text-amber-400 text-[11px] leading-tight">Membalas</span>

                                <span class="font-bold text-gray-800 dark:text-gray-200 text-[11px]" x-text="replyingToMessage ? replyingToMessage.sender_name : ''"></span>

                            </div>

                            <div class="text-gray-600 dark:text-gray-300 truncate text-[11px] italic" x-text="replyingToMessage ? (replyingToMessage.message || (replyingToMessage.attachment_url ? '[Foto]' : '')) : ''"></div>

                        </div>

                    </div>

                    <button type="button" 

                            @click="cancelReply()" 

                            title="Batal balas"

                            class="p-1 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-200/70 dark:hover:bg-gray-700 cursor-pointer transition">

                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>

                    </button>

                </div>



                <!-- 3. Photo Attachment Preview Banner -->

                <div x-show="selectedPhotoPreview" 

                     x-cloak 

                     class="px-4 py-2.5 bg-gray-50 dark:bg-gray-850 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between text-xs transition">

                    <div class="flex items-center gap-3 overflow-hidden">

                        <img :src="selectedPhotoPreview" class="w-12 h-12 rounded-xl object-cover border border-gray-200 dark:border-gray-700 shadow-xs shrink-0">

                        <div class="truncate">

                            <div class="font-bold text-gray-800 dark:text-gray-200 text-xs truncate max-w-xs" x-text="selectedPhotoName"></div>

                            <div class="text-[10px] text-amber-600 dark:text-amber-400 font-semibold mt-0.5">Lampiran foto siap dikirim</div>

                        </div>

                    </div>

                    <button type="button" 

                            @click="cancelPhoto()" 

                            title="Hapus foto"

                            class="p-1.5 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-950/30 cursor-pointer transition">

                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>

                    </button>

                </div>



                <!-- Input Form -->

                <div class="p-4">

                    <form @submit.prevent="editingMessage ? saveEditMessage() : sendMessage()" class="flex items-center gap-2 sm:gap-3">

                        <!-- Hidden Photo Input -->

                        <input type="file" 

                               x-ref="fileInput" 

                               @change="handlePhotoSelect($event)" 

                               accept="image/jpeg,image/png,image/jpg,image/webp" 

                               class="hidden">



                        <!-- Photo Attachment Button -->

                        <button type="button" 

                                @click="$refs.fileInput.click()" 

                                :disabled="editingMessage"

                                title="Lampirkan foto / referensi konsep"

                                class="p-2.5 sm:p-3 rounded-xl border border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400 hover:text-amber-500 hover:border-amber-400 hover:bg-amber-50 dark:hover:bg-gray-700 disabled:opacity-40 transition cursor-pointer shrink-0">

                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>



                        @if(auth()->user()->role === 'photographer')

                        <!-- Custom Offer Button (Photographer Only) -->

                        <button type="button" 

                                @click="openOfferModal = true"

                                :disabled="editingMessage"

                                title="Buat Penawaran Khusus untuk Klien"

                                class="p-2.5 sm:p-3 rounded-xl border border-amber-300 dark:border-amber-700/80 bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 hover:bg-amber-100 hover:border-amber-400 transition cursor-pointer shrink-0 flex items-center gap-1.5 font-bold text-xs">

                            <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z"/></svg>

                            <span class="hidden md:inline">Penawaran Khusus</span>

                        @endif

                        </button>



                        <input type="text" 

                               x-model="newMessage" 

                               @input="handleTyping()"

                               @keydown.enter.prevent="editingMessage ? saveEditMessage() : sendMessage()"

                               @keydown.escape="if (editingMessage) cancelEdit(); else if (replyingToMessage) cancelReply();"

                               :placeholder="editingMessage ? 'Ketik pesan baru...' : (selectedPhotoPreview ? 'Beri keterangan foto (opsional)...' : 'Tulis pesan Anda untuk fotografer...')" 

                               class="flex-1 px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition">

                        

                        <button type="submit" 

                                :disabled="!newMessage.trim() && !selectedPhotoFile" 

                                class="px-5 sm:px-6 py-3 rounded-xl bg-amber-500 hover:bg-amber-600 disabled:opacity-50 text-white font-black text-xs shadow-xs transition cursor-pointer shrink-0"

                                x-text="editingMessage ? 'Simpan' : 'Kirim'">

                        </button>

                    </form>

                </div>

            </div>

        </div>



        <!-- No Active Thread State (Clean WhatsApp-Inspired Empty State) -->

        <div x-show="!activeThread" 

             style="{{ $activeThread ? 'display: none;' : '' }}"

             class="flex-1 flex flex-col items-center justify-between h-full min-h-0 relative overflow-hidden bg-white dark:bg-gray-800 p-8 text-center select-none">

            <!-- Subtle Light Doodle Background Pattern -->

            <div class="absolute inset-0 bg-repeat pointer-events-none opacity-[0.035] dark:hidden" 

                 style="background-image: url('{{ asset('images/chat-bg-pattern-light.png') }}'); background-size: 360px;"></div>

            <!-- Subtle Dark Doodle Background Pattern -->

            <div class="absolute inset-0 bg-repeat pointer-events-none opacity-[0.025] hidden dark:block" 

                 style="background-image: url('{{ asset('images/chat-bg-pattern-dark.png') }}'); background-size: 360px;"></div>



            <!-- Top Spacer for Vertical Centering -->

            <div></div>



            <!-- Center Minimalist Content -->

            <div class="relative z-10 max-w-sm mx-auto flex flex-col items-center">

                <!-- Clean Circular Icon -->

                <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700/60 flex items-center justify-center text-gray-400 dark:text-gray-400 mb-4">

                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">

                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a.75.75 0 01-.818-.847l.487-2.923C3.766 15.748 3 13.974 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" />

                    </svg>

                </div>



                <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100">

                    LensMatch Chat

                </h3>

                

                <p class="text-xs text-gray-400 dark:text-gray-400 mt-1.5 leading-relaxed">

                    Pilih kontak di sebelah kiri untuk membuka percakapan.

                </p>

            </div>



            <!-- Bottom Subtle Encryption Tag -->

            <div class="relative z-10 flex items-center gap-1.5 text-[11px] text-gray-400 dark:text-gray-500 font-medium pb-2">

                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>

                <span>Pesan terenkripsi secara aman</span>

            </div>

        </div>



    </div>



    <!-- Image Lightbox Modal -->

    <div x-show="imageModalOpen" 

         x-cloak 

         @keydown.escape.window="imageModalOpen = false"

         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/85 backdrop-blur-xs">

        <div @click.outside="imageModalOpen = false" class="relative max-w-4xl max-h-[90vh] flex flex-col items-center">

            <button type="button" 

                    @click="imageModalOpen = false" 

                    class="absolute -top-10 right-0 text-white/80 hover:text-white p-1 rounded-lg bg-white/10 hover:bg-white/20 transition cursor-pointer">

                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>

            </button>

            <img :src="modalImageUrl" class="max-h-[85vh] max-w-full rounded-2xl object-contain shadow-2xl">

        </div>

    </div>



    <!-- Custom Offer Modal (Rule #59, #73, #74, #76 Compliant) -->

    <div x-show="openOfferModal" 

         x-cloak 

         class="fixed inset-0 z-50 overflow-y-auto"

         aria-labelledby="offer-modal-title" role="dialog" aria-modal="true">

        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">

            <!-- Backdrop -->

            <div x-show="openOfferModal"

                 x-transition:enter="ease-out duration-300"

                 x-transition:enter-start="opacity-0"

                 x-transition:enter-end="opacity-100"

                 x-transition:leave="ease-in duration-200"

                 x-transition:leave-start="opacity-100"

                 x-transition:leave-end="opacity-0"

                 @click="closeOfferModal()"

                 class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity"></div>



            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>



            <!-- Modal Content -->

            <div x-show="openOfferModal"

                 x-transition:enter="ease-out duration-300"

                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"

                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"

                 x-transition:leave="ease-in duration-200"

                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"

                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"

                 class="relative inline-block align-bottom bg-white dark:bg-gray-850 rounded-2xl p-6 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100 dark:border-gray-700">

                

                <!-- Centered Title (NO ICON, per Rule #59) -->

                <h3 class="text-base sm:text-lg font-black text-gray-900 dark:text-white text-center" id="offer-modal-title">

                    Kirim Penawaran Khusus

                </h3>

                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 text-center max-w-sm mx-auto">

                    Kirimkan rincian kesepakatan sesi foto khusus langsung kepada klien di room chat ini.

                </p>



                <!-- Form Inputs (Rule #73: 2-column grid for short fields) -->

                <form @submit.prevent="submitCustomOffer()" class="mt-5 space-y-4">

                    <!-- Judul Penawaran -->

                    <div>

                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">

                            Judul Penawaran Sesi <span class="text-rose-500">*</span>

                        </label>

                        <input type="text" 

                               x-model="offerForm.judul_penawaran" 

                               required 

                               placeholder="Contoh: Sesi Prewedding Outdoor 3 Hari"

                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 outline-none transition">

                    </div>



                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">

                        <!-- Total Biaya -->

                        <div>

                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">

                                Total Biaya (Rp) <span class="text-rose-500">*</span>

                            </label>

                            <input type="number" 

                                   x-model="offerForm.harga" 

                                   required 

                                   min="10000" 

                                   step="1000"

                                   placeholder="Masukkan total tarif"

                                   class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 outline-none transition">

                        </div>



                        <!-- Durasi (Jam) -->

                        <div>

                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">

                                Durasi Sesi (Jam) <span class="text-rose-500">*</span>

                            </label>

                            <input type="number" 

                                   x-model="offerForm.durasi_jam" 

                                   required 

                                   min="1" 

                                   max="72"

                                   placeholder="Durasi sesi"

                                   class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 outline-none transition">

                        </div>



                        <!-- Tanggal Sesi -->

                        <div>

                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">

                                Tanggal Sesi <span class="text-rose-500">*</span>

                            </label>

                            <input type="date" 

                                   x-model="offerForm.booking_date" 

                                   required 

                                   class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 outline-none transition">

                        </div>



                        <!-- Jam Mulai Sesi -->

                        <div>

                            <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">

                                Jam Mulai <span class="text-rose-500">*</span>

                            </label>

                            <input type="time" 

                                   x-model="offerForm.booking_time" 

                                   required 

                                   class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 outline-none transition">

                        </div>

                    </div>



                    <!-- Lokasi Pemotretan -->

                    <div>

                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">

                            Lokasi Pemotretan <span class="text-rose-500">*</span>

                        </label>

                        <input type="text" 

                               x-model="offerForm.lokasi" 

                               required 

                               placeholder="Contoh: Pantai Melasti & Tebing Karang Boma, Uluwatu"

                               class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 outline-none transition">

                    </div>



                    <!-- Deskripsi Rincian -->

                    <div>

                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-1">

                            Rincian Layanan & Catatan Khusus

                        </label>

                        <textarea x-model="offerForm.deskripsi" 

                                  rows="3" 

                                  placeholder="Tuliskan detail paket, output file foto, akomodasi, atau catatan yang disepakati..."

                                  class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 outline-none transition resize-none"></textarea>

                    </div>



                    <!-- Action Buttons: Centered, Batal on Left, Max 2 buttons (Rule #59 & Rule #74) -->

                    <div class="pt-3 flex items-center justify-center gap-3">

                        <button type="button" 

                                @click="closeOfferModal()"

                                class="px-5 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-750 text-gray-700 dark:text-gray-300 font-bold text-xs transition cursor-pointer">

                            Batal

                        </button>

                        <button type="submit" 

                                :disabled="offerSubmitting"

                                class="px-5 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold text-xs shadow-xs transition cursor-pointer disabled:opacity-60">

                            <span x-show="!offerSubmitting">Kirim Penawaran</span>

                            <span x-show="offerSubmitting">Mengirim...</span>

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>



    <!-- Delete Message Modal (Compliant with Project Rule #59) -->

    <div x-show="deleteModalOpen" 

         x-cloak 

         class="fixed inset-0 z-50 overflow-y-auto"

         aria-labelledby="modal-title" role="dialog" aria-modal="true">

        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">

            <!-- Backdrop -->

            <div x-show="deleteModalOpen"

                 x-transition:enter="ease-out duration-300"

                 x-transition:enter-start="opacity-0"

                 x-transition:enter-end="opacity-100"

                 x-transition:leave="ease-in duration-200"

                 x-transition:leave-start="opacity-100"

                 x-transition:leave-end="opacity-0"

                 @click="closeDeleteModal()"

                 class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity"></div>



            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>



            <!-- Modal Dialog -->

            <div x-show="deleteModalOpen"

                 x-transition:enter="ease-out duration-300"

                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"

                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"

                 x-transition:leave="ease-in duration-200"

                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"

                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"

                 class="relative inline-block align-bottom bg-white dark:bg-gray-850 rounded-2xl p-6 text-center overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-gray-100 dark:border-gray-700">

                

                <!-- Close Button 'X' in Top-Right Corner (Only when Batal button is not shown at bottom per Rule #59) -->

                <button type="button" 

                        x-show="messageToDelete && messageToDelete.can_delete_everyone"

                        @click="closeDeleteModal()" 

                        class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-750 transition cursor-pointer">

                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>

                </button>



                <!-- Centered Title (NO ICON, per Rule #59) -->

                <h3 class="text-lg font-black text-gray-900 dark:text-white mt-1 text-center" id="modal-title">

                    Hapus Pesan?

                </h3>



                <!-- Description -->

                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 text-center max-w-sm mx-auto">

                    <template x-if="messageToDelete && messageToDelete.can_delete_everyone">

                        <span>Apakah Anda ingin menghapus pesan ini hanya untuk Anda, atau untuk semua orang di percakapan ini?</span>

                    </template>

                    <template x-if="messageToDelete && !messageToDelete.can_delete_everyone">

                        <span>Pesan ini akan dihapus dari riwayat percakapan Anda.</span>

                    </template>

                </p>



                <!-- Preview of message to delete -->

                <div class="mt-3.5 p-3 rounded-xl bg-gray-50 dark:bg-gray-800 text-left border border-gray-100 dark:border-gray-700">

                    <p class="text-xs text-gray-600 dark:text-gray-300 line-clamp-2 italic" x-text="messageToDelete ? (messageToDelete.message || (messageToDelete.attachment_url ? '[Foto]' : '')) : ''"></p>

                </div>



                <!-- Action Buttons Centered at Bottom (Max 2 Buttons, per Rule #59) -->

                <div class="mt-6 flex flex-col sm:flex-row items-center justify-center gap-3">

                    <template x-if="messageToDelete && messageToDelete.can_delete_everyone">

                        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 w-full">

                            <button type="button" 

                                    @click="confirmDeleteMessage('for_everyone')"

                                    class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-xs shadow-xs transition cursor-pointer">

                                Hapus untuk Semua Orang

                            </button>

                            <button type="button" 

                                    @click="confirmDeleteMessage('for_me')"

                                    class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-650 text-gray-700 dark:text-gray-200 font-bold text-xs transition cursor-pointer">

                                Hapus untuk Saya

                            </button>

                        </div>

                    </template>

                    <template x-if="messageToDelete && !messageToDelete.can_delete_everyone">

                        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 w-full">

                            <button type="button" 

                                    @click="confirmDeleteMessage('for_me')"

                                    class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-xs shadow-xs transition cursor-pointer">

                                Hapus untuk Saya

                            </button>

                            <button type="button" 

                                    @click="closeDeleteModal()"

                                    class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-650 text-gray-700 dark:text-gray-200 font-bold text-xs transition cursor-pointer">

                                Batal

                            </button>

                        </div>

                    </template>

                </div>

            </div>

        </div>

    </div>



    <!-- Toast Notification -->

    <div x-show="toastShow" 

         x-cloak 

         x-transition:enter="ease-out duration-300"

         x-transition:enter-start="opacity-0 -translate-y-2"

         x-transition:enter-end="opacity-100 translate-y-0"

         x-transition:leave="ease-in duration-200"

         x-transition:leave-start="opacity-100 translate-y-0"

         x-transition:leave-end="opacity-0 -translate-y-2"

         class="fixed top-6 right-6 z-50 bg-gray-900/90 backdrop-blur-md text-white text-xs px-4 py-2.5 rounded-xl shadow-lg border border-gray-800 flex items-center gap-2">

        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>

        <span x-text="toastMessage"></span>

    </div>



</div>



<script>

window.formatLocalTime = function(isoStr, fallbackTime) {

    const defaultVal = fallbackTime || isoStr || '';

    if (!isoStr && !fallbackTime) return '';

    try {

        let valToParse = String(isoStr || fallbackTime).trim();

        if (valToParse.indexOf(' ') !== -1 && valToParse.indexOf('T') === -1) {

            valToParse = valToParse.replace(' ', 'T');

        }

        const d = new Date(valToParse);

        if (!isNaN(d.getTime())) {

            const hours = ('0' + d.getHours()).slice(-2);

            const minutes = ('0' + d.getMinutes()).slice(-2);

            return hours + ':' + minutes;

        }

        return defaultVal;

    } catch(e) {

        return defaultVal;

    }

};



function clientChatApp(initialThreads, activeThreadId, initialMessages, initialStatus) {

    return {

        threads: initialThreads || [],

        activeThreadId: activeThreadId,

        activeThread: null,

        messages: initialMessages || [],

        newMessage: '',

        isSending: false,

        chatListCollapsed: false,

        userStatus: initialStatus || { is_online: false, label: 'Aktif 7+ hari lalu', color: 'text-gray-400', dot_color: 'bg-gray-400' },

        

        // State for edit, delete, and toast

        editingMessage: null,

        deleteModalOpen: false,

        messageToDelete: null,

        toastShow: false,

        toastMessage: '',

        toastTimeout: null,



        // State for Custom Offer

        openOfferModal: false,

        offerSubmitting: false,

        offerForm: {

            judul_penawaran: '',

            booking_date: new Date().toISOString().split('T')[0],

            booking_time: '09:00',

            durasi_jam: 3,

            lokasi: '',

            harga: '',

            deskripsi: ''

        },



        // State for Quote Reply & Photo Attachments

        replyingToMessage: null,

        selectedPhotoFile: null,

        selectedPhotoPreview: null,

        selectedPhotoName: '',

        imageModalOpen: false,

        modalImageUrl: '',



        // State for Typing Indicator

        otherIsTyping: false,

        lastTypingSent: 0,



        // State for In-Chat Search

        searchBarOpen: false,

        searchKeyword: '',

        searchResults: [],

        currentSearchIndex: -1,

        searchResultsText: '',

        highlightedMsgId: null,

        highlightTimeout: null,

        currentEchoChannel: null,

        

        init() {

            if (this.activeThreadId && this.threads.length > 0) {

                this.activeThread = this.threads.find(t => t.id === this.activeThreadId) || null;

            } else {

                this.activeThread = null;

            }

            if (this.activeThreadId && this.activeThread) {

                this.$nextTick(() => {

                    this.scrollToBottom();

                    setTimeout(() => this.scrollToBottom(), 50);

                    setTimeout(() => this.scrollToBottom(), 150);

                    setTimeout(() => this.scrollToBottom(), 300);

                });

                this.fetchMessages(true);

                this.listenToEcho(this.activeThreadId);

                // Background polling sebagai fallback ketahanan jika WebSocket terputus

                setInterval(() => this.fetchMessages(), 5000);

            }

        },



        listenToEcho(threadId) {

            if (!threadId) return;

            if (window.Echo) {

                try {

                    if (this.currentEchoChannel && this.currentEchoChannel !== threadId) {

                        window.Echo.leave('chat.' + this.currentEchoChannel);

                    }

                    this.currentEchoChannel = threadId;

                    window.Echo.private('chat.' + threadId)

                        .listen('.MessageSent', (e) => {

                            if (e && e.message) {

                                const exists = this.messages.some(m => m.id === e.message.id);

                                if (!exists) {

                                    this.messages.push(e.message);

                                    this.scrollToBottom();

                                }

                            }

                        });

                } catch (err) {

                    console.log('Echo listen note:', err);

                }

            }

        },



        switchThread(threadId) {

            if (this.activeThreadId === threadId) return;

            this.activeThreadId = threadId;



            // Reset editing, reply, search, photo on thread switch

            this.cancelEdit();

            this.cancelReply();

            this.cancelPhoto();

            this.closeSearchBar();



            // 1. Update Browser URL Tanpa Reload Halaman (Seamless Single-Page App)

            window.history.pushState({ threadId: threadId }, '', '?thread=' + threadId);



            // 2. Set metadata thread aktif & reset unread lokal

            const target = this.threads.find(t => t.id === threadId);

            if (target) {

                this.activeThread = target;

                target.unread_count = 0;

                if (target.status_user) {

                    this.userStatus = target.status_user;

                }

            }



            // 3. Pasang pendengar WebSocket Reverb untuk thread baru

            this.listenToEcho(threadId);



            // 4. Ambil pesan thread baru secara instan & scroll to bottom

            this.messages = [];

            this.fetchMessages(true);

        },



        fetchMessages(isInitial = false) {

            if (!this.activeThreadId) return;

            fetch('/chat/messages/' + this.activeThreadId)

                .then(res => res.json())

                .then(data => {

                    if (data.success) {

                        if (data.other_status) {

                            this.userStatus = data.other_status;

                            if (this.activeThread) {

                                this.activeThread.status_user = data.other_status;

                            }

                        }



                        // Update typing indicator

                        this.otherIsTyping = Boolean(data.other_is_typing);



                        // Update current active thread info if needed

                        if (this.activeThread) {

                            if (data.other_name) this.activeThread.name = data.other_name;

                            if (data.other_avatar) this.activeThread.foto = data.other_avatar;

                            if (data.other_initial) this.activeThread.initial = data.other_initial;

                            if (data.photographer_id !== undefined) this.activeThread.photographer_id = data.photographer_id;

                            if (data.client_id !== undefined) this.activeThread.client_id = data.client_id;

                            if (data.user_id !== undefined) this.activeThread.user_id = data.user_id;

                        }



                        const isAtBottom = this.checkIsAtBottom();

                        const prevCount = this.messages.length;

                        

                        // Keep pending optimistic messages that haven't been resolved yet

                        const pendingMsgs = this.messages.filter(m => String(m.id).startsWith('temp_'));

                        this.messages = [...data.messages, ...pendingMsgs];

                        

                        // Update last message snippet on the thread list in real time

                        if (data.messages.length > 0) {

                            const lastMsg = data.messages[data.messages.length - 1];

                            const threadInList = this.threads.find(t => t.id === this.activeThreadId);

                            if (threadInList) {

                                threadInList.last_message = lastMsg.attachment_url && !lastMsg.message ? '[Foto]' : lastMsg.message;

                                threadInList.last_message_time = lastMsg.time;

                                threadInList.last_message_iso = lastMsg.created_at;

                            }

                        }



                        if (isInitial || (this.messages.length > prevCount && isAtBottom)) {

                            this.$nextTick(() => {

                                this.scrollToBottom();

                                setTimeout(() => this.scrollToBottom(), 50);

                                setTimeout(() => this.scrollToBottom(), 150);

                                setTimeout(() => this.scrollToBottom(), 300);

                            });

                        }

                    }

                })

                .catch(err => console.log('Client chat fetch error:', err));

        },



        // Typing heartbeat

        handleTyping() {

            if (!this.activeThreadId) return;

            const now = Date.now();

            if (this.lastTypingSent && (now - this.lastTypingSent) < 2500) {

                return;

            }

            this.lastTypingSent = now;

            const tokenEl = document.querySelector('meta[name=csrf-token]');

            const csrfToken = tokenEl ? tokenEl.content : '';

            

            fetch('/chat/typing', {

                method: 'POST',

                headers: {

                    'Content-Type': 'application/json',

                    'X-CSRF-TOKEN': csrfToken,

                    'Accept': 'application/json'

                },

                body: JSON.stringify({ id_chat_booking: this.activeThreadId })

            }).catch(() => {});

        },



        sendMessage() {

            if ((!this.newMessage.trim() && !this.selectedPhotoFile) || !this.activeThreadId) return;

            

            const textToSend = this.newMessage.trim();

            const replyTarget = this.replyingToMessage;

            const photoFile = this.selectedPhotoFile;

            const photoPreview = this.selectedPhotoPreview;



            // Reset Input UI Instantly

            this.newMessage = '';

            this.replyingToMessage = null;

            this.selectedPhotoFile = null;

            this.selectedPhotoPreview = null;

            this.selectedPhotoName = '';

            if (this.$refs.fileInput) this.$refs.fileInput.value = '';



            // 1. INSTANT OPTIMISTIC UI

            const tempId = 'temp_' + Date.now();

            const now = new Date();

            const timeStr = ('0' + now.getHours()).slice(-2) + ':' + ('0' + now.getMinutes()).slice(-2);

            

            const optimisticMsg = {

                id: tempId,

                id_sender: {{ auth()->id() }},

                sender_name: '{{ addslashes(auth()->user()->nama ?? 'Saya') }}',

                message: textToSend,

                reply_to: replyTarget ? {

                    id: replyTarget.id,

                    id_sender: replyTarget.id_sender,

                    sender_name: replyTarget.sender_name,

                    message: replyTarget.message,

                    attachment_url: replyTarget.attachment_url

                } : null,

                attachment_url: photoPreview || null,

                attachment_type: photoFile ? 'image' : null,

                is_me: true,

                is_read: false,

                created_at: now.toISOString(),

                time: timeStr,

                pending: true,

                can_edit: !photoFile,

                can_delete_everyone: true,

                deleted_for_all: false,

                is_edited: false

            };



            this.messages.push(optimisticMsg);

            this.$nextTick(() => this.scrollToBottom());



            // 2. REAL-TIME THREAD LIST SNIPPET UPDATE & FLOAT TO TOP

            const threadInList = this.threads.find(t => t.id === this.activeThreadId);

            if (threadInList) {

                threadInList.last_message = textToSend ? (photoFile ? '[Foto] ' + textToSend : textToSend) : '[Foto]';

                threadInList.last_message_time = timeStr;

                threadInList.last_message_iso = now.toISOString();



                const currentIdx = this.threads.indexOf(threadInList);

                if (currentIdx > 0) {

                    this.threads.splice(currentIdx, 1);

                    this.threads.unshift(threadInList);

                }

            }



            // 3. Background Asynchronous Dispatch (FormData jika ada foto, JSON jika teks)

            const tokenEl = document.querySelector('meta[name=csrf-token]');

            const csrfToken = tokenEl ? tokenEl.content : '';

            

            let fetchOptions;

            if (photoFile) {

                const formData = new FormData();

                formData.append('id_chat_booking', this.activeThreadId);

                if (textToSend) formData.append('message', textToSend);

                if (replyTarget) formData.append('reply_to_id', replyTarget.id);

                formData.append('attachment', photoFile);



                fetchOptions = {

                    method: 'POST',

                    headers: {

                        'X-CSRF-TOKEN': csrfToken,

                        'Accept': 'application/json'

                    },

                    body: formData

                };

            } else {

                fetchOptions = {

                    method: 'POST',

                    headers: {

                        'Content-Type': 'application/json',

                        'X-CSRF-TOKEN': csrfToken,

                        'Accept': 'application/json'

                    },

                    body: JSON.stringify({

                        id_chat_booking: this.activeThreadId,

                        message: textToSend,

                        reply_to_id: replyTarget ? replyTarget.id : null

                    })

                };

            }



            fetch('/chat/send', fetchOptions)

                .then(res => res.json())

                .then(data => {

                    if (data.success && data.message) {

                        const idx = this.messages.findIndex(m => m.id === tempId);

                        if (idx !== -1) {

                            this.messages[idx] = data.message;

                        }

                    } else if (data.message) {

                        window.dispatchEvent(new CustomEvent('show-toast', { detail: { type: 'error', message: data.message } }));

                        this.messages = this.messages.filter(m => m.id !== tempId);

                    }

                })

                .catch(err => {

                    console.error('Client send message failed:', err);

                });

        },



        // Quote Reply Feature

        startReplyMessage(m) {

            if (!m || m.deleted_for_all) return;

            this.cancelEdit();

            this.replyingToMessage = m;

            this.$nextTick(() => {

                const inputEl = document.querySelector('input[x-model="newMessage"]');

                if (inputEl) inputEl.focus();

            });

        },



        cancelReply() {

            this.replyingToMessage = null;

        },



        // Photo Attachment Feature

        handlePhotoSelect(event) {

            const file = event.target.files[0];

            if (!file) return;

            if (file.size > 5 * 1024 * 1024) {

                window.dispatchEvent(new CustomEvent('show-toast', { detail: { type: 'error', message: 'Ukuran file foto maksimal adalah 5MB' } }));

                event.target.value = '';

                return;

            }

            this.selectedPhotoFile = file;

            this.selectedPhotoName = file.name;

            const reader = new FileReader();

            reader.onload = (e) => {

                this.selectedPhotoPreview = e.target.result;

            };

            reader.readAsDataURL(file);

        },



        cancelPhoto() {

            this.selectedPhotoFile = null;

            this.selectedPhotoPreview = null;

            this.selectedPhotoName = '';

            if (this.$refs.fileInput) this.$refs.fileInput.value = '';

        },



        openImageModal(url) {

            if (!url) return;

            this.modalImageUrl = url;

            this.imageModalOpen = true;

        },



        // In-Chat Search Feature

        toggleSearchBar() {

            this.searchBarOpen = !this.searchBarOpen;

            if (this.searchBarOpen) {

                this.$nextTick(() => {

                    if (this.$refs.searchInput) this.$refs.searchInput.focus();

                });

            } else {

                this.closeSearchBar();

            }

        },



        closeSearchBar() {

            this.searchBarOpen = false;

            this.searchKeyword = '';

            this.searchResults = [];

            this.currentSearchIndex = -1;

            this.searchResultsText = '';

            this.highlightedMsgId = null;

        },



        performSearch() {

            const q = this.searchKeyword.trim().toLowerCase();

            if (!q) {

                this.searchResults = [];

                this.currentSearchIndex = -1;

                this.searchResultsText = '';

                this.highlightedMsgId = null;

                return;

            }

            this.searchResults = this.messages.filter(m => !m.deleted_for_all && m.message && m.message.toLowerCase().includes(q));

            if (this.searchResults.length > 0) {

                this.currentSearchIndex = 0;

                this.searchResultsText = `1 dari ${this.searchResults.length}`;

                this.scrollToMessage(this.searchResults[0].id);

            } else {

                this.currentSearchIndex = -1;

                this.searchResultsText = 'Tidak ditemukan';

                this.highlightedMsgId = null;

            }

        },



        nextSearchResult() {

            if (this.searchResults.length === 0) return;

            this.currentSearchIndex = (this.currentSearchIndex + 1) % this.searchResults.length;

            this.searchResultsText = `${this.currentSearchIndex + 1} dari ${this.searchResults.length}`;

            this.scrollToMessage(this.searchResults[this.currentSearchIndex].id);

        },



        prevSearchResult() {

            if (this.searchResults.length === 0) return;

            this.currentSearchIndex = (this.currentSearchIndex - 1 + this.searchResults.length) % this.searchResults.length;

            this.searchResultsText = `${this.currentSearchIndex + 1} dari ${this.searchResults.length}`;

            this.scrollToMessage(this.searchResults[this.currentSearchIndex].id);

        },



        scrollToMessage(id) {

            if (!id) return;

            this.highlightedMsgId = id;

            this.$nextTick(() => {

                const el = document.getElementById('chat_msg_' + id);

                if (el) {

                    el.scrollIntoView({ behavior: 'smooth', block: 'center' });

                }

            });

            if (this.highlightTimeout) clearTimeout(this.highlightTimeout);

            this.highlightTimeout = setTimeout(() => {

                this.highlightedMsgId = null;

            }, 2200);

        },



        // Edit Message Feature (Batas 15 menit)

        startEditMessage(m) {

            if (!m || !m.can_edit || m.deleted_for_all || m.attachment_url) return;

            this.cancelReply();

            this.editingMessage = m;

            this.newMessage = m.message;

            this.$nextTick(() => {

                const inputEl = document.querySelector('input[x-model="newMessage"]');

                if (inputEl) {

                    inputEl.focus();

                    inputEl.setSelectionRange(inputEl.value.length, inputEl.value.length);

                }

            });

        },



        cancelEdit() {

            this.editingMessage = null;

            this.newMessage = '';

        },



        saveEditMessage() {

            if (!this.editingMessage || !this.newMessage.trim()) return;

            const msgId = this.editingMessage.id;

            const updatedText = this.newMessage.trim();



            // Optimistic update

            const target = this.messages.find(m => m.id === msgId);

            if (target) {

                target.message = updatedText;

                target.is_edited = true;

            }

            this.editingMessage = null;

            this.newMessage = '';



            const tokenEl = document.querySelector('meta[name=csrf-token]');

            const csrfToken = tokenEl ? tokenEl.content : '';



            fetch('/chat/messages/' + msgId, {

                method: 'PATCH',

                headers: {

                    'Content-Type': 'application/json',

                    'X-CSRF-TOKEN': csrfToken,

                    'Accept': 'application/json'

                },

                body: JSON.stringify({ message: updatedText })

            })

            .then(res => res.json())

            .then(data => {

                if (data.success && data.message) {

                    const idx = this.messages.findIndex(m => m.id === msgId);

                    if (idx !== -1) {

                        this.messages[idx] = data.message;

                    }

                } else if (data.message) {

                    window.dispatchEvent(new CustomEvent('show-toast', { detail: { type: 'error', message: data.message } }));

                    this.fetchMessages();

                }

            })

            .catch(err => {

                console.error('Failed to edit message:', err);

                this.fetchMessages();

            });

        },



        // Delete / Unsend Feature (Rule #59 Compliant)

        promptDeleteMessage(m) {

            if (!m || m.pending) return;

            this.messageToDelete = m;

            this.deleteModalOpen = true;

        },



        closeDeleteModal() {

            this.deleteModalOpen = false;

            this.messageToDelete = null;

        },



        confirmDeleteMessage(mode) {

            if (!this.messageToDelete) return;

            const msgId = this.messageToDelete.id;

            this.closeDeleteModal();



            // Optimistic UI

            if (mode === 'for_everyone') {

                const target = this.messages.find(m => m.id === msgId);

                if (target) {

                    target.deleted_for_all = true;

                    target.message = '[Pesan ini telah dihapus]';

                    target.attachment_url = null;

                    target.can_edit = false;

                    target.can_delete_everyone = false;

                }

            } else {

                this.messages = this.messages.filter(m => m.id !== msgId);

            }



            const tokenEl = document.querySelector('meta[name=csrf-token]');

            const csrfToken = tokenEl ? tokenEl.content : '';



            fetch('/chat/messages/' + msgId, {

                method: 'DELETE',

                headers: {

                    'Content-Type': 'application/json',

                    'X-CSRF-TOKEN': csrfToken,

                    'Accept': 'application/json'

                },

                body: JSON.stringify({ mode: mode })

            })

            .then(res => res.json())

            .then(data => {

                if (data.success) {

                    if (mode === 'for_everyone' && data.message) {

                        const idx = this.messages.findIndex(m => m.id === msgId);

                        if (idx !== -1) {

                            this.messages[idx] = data.message;

                        }

                    }

                } else if (data.message) {

                    window.dispatchEvent(new CustomEvent('show-toast', { detail: { type: 'error', message: data.message } }));

                    this.fetchMessages();

                }

            })

            .catch(err => {

                console.error('Failed to delete message:', err);

                this.fetchMessages();

            });

        },



        // Copy Message Text & Toast Feedback

        copyMessage(text) {

            if (!text) return;

            if (navigator.clipboard && navigator.clipboard.writeText) {

                navigator.clipboard.writeText(text).then(() => {

                    this.showToastNotification('Pesan berhasil disalin');

                }).catch(() => {

                    this.fallbackCopy(text);

                });

            } else {

                this.fallbackCopy(text);

            }

        },



        fallbackCopy(text) {

            const el = document.createElement('textarea');

            el.value = text;

            el.style.position = 'fixed';

            el.style.opacity = '0';

            document.body.appendChild(el);

            el.select();

            try {

                document.execCommand('copy');

                this.showToastNotification('Pesan berhasil disalin');

            } catch (e) {

                console.error('Copy fallback error:', e);

            }

            document.body.removeChild(el);

        },



        showToastNotification(msg) {

            this.toastMessage = msg;

            this.toastShow = true;

            if (this.toastTimeout) clearTimeout(this.toastTimeout);

            this.toastTimeout = setTimeout(() => {

                this.toastShow = false;

            }, 2500);

        },



        checkIsAtBottom() {

            const box = this.$refs.msgContainer;

            if (!box) return true;

            return box.scrollHeight - box.scrollTop - box.clientHeight < 100;

        },



        closeOfferModal() {

            this.openOfferModal = false;

            this.offerForm = {

                judul_penawaran: '',

                booking_date: new Date().toISOString().split('T')[0],

                booking_time: '09:00',

                durasi_jam: 3,

                lokasi: '',

                harga: '',

                deskripsi: ''

            };

        },



        submitCustomOffer() {

            if (!this.activeThreadId || this.offerSubmitting) return;

            this.offerSubmitting = true;

            const tokenEl = document.querySelector('meta[name=csrf-token]');

            const csrfToken = tokenEl ? tokenEl.content : '';



            fetch('/chat/custom-offer', {

                method: 'POST',

                headers: {

                    'Content-Type': 'application/json',

                    'X-CSRF-TOKEN': csrfToken,

                    'Accept': 'application/json'

                },

                body: JSON.stringify({

                    id_chat_booking: this.activeThreadId,

                    ...this.offerForm

                })

            })

            .then(res => res.json())

            .then(data => {

                this.offerSubmitting = false;

                if (data.success && (data.chat_message || data.message)) {

                    this.closeOfferModal();

                    const newMsg = data.chat_message || data.message;

                    this.messages.push(newMsg);

                    this.$nextTick(() => this.scrollToBottom());

                    this.showToastNotification('Penawaran khusus berhasil dikirim');

                } else {

                    window.dispatchEvent(new CustomEvent('show-toast', { 

                        detail: { type: 'error', message: data.message || 'Gagal mengirim penawaran khusus' } 

                    }));

                }

            })

            .catch(err => {

                this.offerSubmitting = false;

                console.error('Submit custom offer error:', err);

            });

        },



        acceptOffer(offerId) {
            if (!offerId) return;
            window.dispatchEvent(new CustomEvent('open-confirm-modal', {
                detail: {
                    title: 'Setujui Penawaran Khusus?',
                    message: 'Apakah Anda setuju dengan rincian penawaran khusus ini dan ingin melanjutkan ke pembayaran invoice?',
                    confirmText: 'Ya, Setujui & Bayar',
                    type: 'primary',
                    buttonClass: 'bg-amber-400 hover:bg-amber-500 text-gray-950 font-extrabold shadow-xs',
                    onConfirm: () => this.doAcceptOffer(offerId)
                }
            }));
        },

        doAcceptOffer(offerId) {
            const tokenEl = document.querySelector('meta[name=csrf-token]');
            const csrfToken = tokenEl ? tokenEl.content : '';

            fetch('/chat/custom-offer/' + offerId + '/accept', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                const targetUrl = data.redirect_url || data.invoice_url;
                if (data.success && targetUrl) {
                    window.location.href = targetUrl;
                } else {
                    window.dispatchEvent(new CustomEvent('show-toast', {
                        detail: { type: 'error', message: data.message || 'Gagal menyetujui penawaran' }
                    }));
                }
            })
            .catch(err => console.error('Accept offer error:', err));
        },

        rejectOffer(offerId) {
            if (!offerId) return;
            window.dispatchEvent(new CustomEvent('open-confirm-modal', {
                detail: {
                    title: 'Tolak Penawaran Khusus?',
                    message: 'Apakah Anda yakin ingin menolak penawaran khusus ini dari mitra fotografer?',
                    confirmText: 'Ya, Tolak Penawaran',
                    type: 'danger',
                    buttonClass: 'bg-rose-600 hover:bg-rose-700 text-white font-bold',
                    onConfirm: () => this.doRejectOffer(offerId)
                }
            }));
        },

        doRejectOffer(offerId) {
            const tokenEl = document.querySelector('meta[name=csrf-token]');
            const csrfToken = tokenEl ? tokenEl.content : '';

            fetch('/chat/custom-offer/' + offerId + '/reject', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.dispatchEvent(new CustomEvent('show-toast', {
                        detail: { type: 'info', message: 'Penawaran khusus telah ditolak.' }
                    }));
                    this.fetchMessages();
                } else {
                    window.dispatchEvent(new CustomEvent('show-toast', {
                        detail: { type: 'error', message: data.message || 'Gagal menolak penawaran' }
                    }));
                }
            })
            .catch(err => console.error('Reject offer error:', err));
        },



        scrollToBottom(smooth = false) {

            const box = this.$refs.msgContainer;

            if (!box) return;

            if (smooth) {

                box.scrollTo({ top: box.scrollHeight + 1000, behavior: 'smooth' });

            } else {

                box.scrollTop = box.scrollHeight + 1000;

            }

        }

    };

}

window.clientChatApp = clientChatApp;

</script>

@endsection