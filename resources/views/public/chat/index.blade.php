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
         :class="chatListCollapsed ? 'w-20' : 'w-full sm:w-80 lg:w-96'">
        
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
                <!-- Icon Collapse (Panah Kiri Berongga) -->
                <svg x-show="!chatListCollapsed" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.75 19.5l-7.5-7.5 7.5-7.5m-6 15L5.25 12l7.5-7.5" />
                </svg>
                <!-- Icon Expand (Panah Kanan Berongga) -->
                <svg x-show="chatListCollapsed" class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" style="display: none;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 4.5l7.5 7.5-7.5 7.5m-6-15l7.5 7.5-7.5 7.5" />
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
                            activeThreadId === t.id ? 'bg-amber-50 dark:bg-amber-950/40 border-l-4 border-amber-500' : ''
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
                        <span class="absolute bottom-0 right-0 w-3 h-3 rounded-full border-2 border-white dark:border-gray-800"
                              :class="t.status_user ? t.status_user.dot_color : 'bg-gray-400'"></span>
                        
                        <!-- Unread pulse dot when collapsed -->
                        <template x-if="chatListCollapsed && t.unread_count > 0">
                            <span class="absolute -top-1 -right-1 w-3.5 h-3.5 rounded-full bg-rose-500 border-2 border-white dark:border-gray-800 animate-pulse"></span>
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
    <div class="flex-1 flex flex-col h-full min-h-0 overflow-hidden bg-white dark:bg-gray-800">
        
        <!-- When Active Thread Selected -->
        <div x-show="activeThread" class="flex-1 flex flex-col h-full min-h-0 overflow-hidden">
            
            <!-- Active Photographer Header -->
            <div class="p-4 sm:px-6 border-b border-gray-200 dark:border-gray-700/80 flex items-center justify-between bg-white dark:bg-gray-800 shrink-0 z-20">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-amber-500 flex items-center justify-center text-white font-black text-xs shadow-xs overflow-hidden">
                        <template x-if="activeThread && activeThread.foto">
                            <img :src="activeThread.foto" class="w-full h-full object-cover" x-on:error="$el.style.display='none'; if ($el.nextElementSibling) $el.nextElementSibling.style.display='flex'">
                        </template>
                        <div class="w-full h-full flex items-center justify-center" :style="(activeThread && activeThread.foto) ? 'display: none;' : ''">
                            <span x-text="activeThread ? activeThread.initial : ''"></span>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-gray-900 dark:text-white" x-text="activeThread ? activeThread.name : ''"></h3>
                        <p class="text-[11px] font-semibold flex items-center gap-1.5" :class="userStatus.color">
                            <span class="w-2 h-2 rounded-full shrink-0" :class="userStatus.dot_color"></span>
                            <span x-text="userStatus.label"></span>
                        </p>
                    </div>
                </div>
                <template x-if="activeThread && activeThread.photographer_id">
                    <a :href="'/fotografer/' + activeThread.photographer_id" 
                       class="text-xs font-bold text-amber-500 hover:text-amber-600 dark:hover:text-amber-400 transition flex items-center gap-1">
                        <span>Lihat Profil Fotografer</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"></path></svg>
                    </a>
                </template>
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
                                <div :class="m.is_me ? 'bg-amber-500 text-white rounded-2xl rounded-tr-none shadow-xs' : 'bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 border border-gray-200 dark:border-gray-700 rounded-2xl rounded-tl-none shadow-xs'"
                                     class="max-w-md px-4 py-2.5 space-y-1 relative group">
                                    
                                    <!-- Message Content Body -->
                                    <p class="text-xs leading-relaxed break-words whitespace-pre-line pr-2" x-text="m.message"></p>

                                    <!-- WhatsApp Style Bottom-Right Timestamp & Tick Indicators -->
                                    <div class="flex items-center justify-end gap-1 text-[10px] select-none pt-0.5" 
                                         :class="m.is_me ? 'text-amber-100/90' : 'text-gray-400'">
                                        <span x-text="formatLocalTime(m.created_at || m.time, m.time)"></span>

                                        <!-- WhatsApp Ticks (Only for My Sent Messages) -->
                                        <template x-if="m.is_me">
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

            <!-- Input Message Bar -->
            <div class="p-4 border-t border-gray-200 dark:border-gray-700/80 bg-white dark:bg-gray-800 shrink-0 z-20">
                <form @submit.prevent="sendMessage()" class="flex items-center gap-3">
                    <input type="text" 
                           x-model="newMessage" 
                           @keydown.enter.prevent="sendMessage()"
                           placeholder="Tulis pesan Anda untuk fotografer..." 
                           class="flex-1 px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition">
                    <button type="submit" 
                            :disabled="!newMessage.trim()" 
                            class="px-6 py-3 rounded-xl bg-amber-500 hover:bg-amber-600 disabled:opacity-50 text-white font-black text-xs shadow-xs transition cursor-pointer">
                        Kirim
                    </button>
                </form>
            </div>
        </div>

        <!-- No Active Thread State -->
        <div x-show="!activeThread" class="flex-1 flex flex-col items-center justify-center p-8 text-center space-y-3">
            <div class="w-16 h-16 rounded-full bg-amber-50 dark:bg-amber-950/60 text-amber-500 flex items-center justify-center">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
            </div>
            <h3 class="text-base font-bold text-gray-900 dark:text-white">Pilih Percakapan Fotografer</h3>
            <p class="text-xs text-gray-500 max-w-sm">Pilih salah satu kontak fotografer di panel kiri untuk mulai berkonsultasi mengenai paket dan jadwal foto Anda.</p>
        </div>

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
        
        init() {
            if (this.activeThreadId && this.threads.length > 0) {
                this.activeThread = this.threads.find(t => t.id === this.activeThreadId) || this.threads[0];
            }
            if (this.activeThreadId) {
                this.fetchMessages();
                setInterval(() => this.fetchMessages(), 2500);
            }
        },

        switchThread(threadId) {
            if (this.activeThreadId === threadId) return;
            this.activeThreadId = threadId;

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

            // 3. Ambil pesan thread baru secara instan
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

                        // Update current active thread info if needed
                        if (this.activeThread) {
                            if (data.other_name) this.activeThread.name = data.other_name;
                            if (data.other_avatar) this.activeThread.foto = data.other_avatar;
                            if (data.other_initial) this.activeThread.initial = data.other_initial;
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
                                threadInList.last_message = lastMsg.message;
                                threadInList.last_message_time = lastMsg.time;
                                threadInList.last_message_iso = lastMsg.created_at;
                            }
                        }

                        if (isInitial || (this.messages.length > prevCount && isAtBottom)) {
                            this.$nextTick(() => this.scrollToBottom());
                        }
                    }
                })
                .catch(err => console.log('Client chat fetch error:', err));
        },

        sendMessage() {
            if (!this.newMessage.trim() || !this.activeThreadId) return;
            const textToSend = this.newMessage.trim();
            this.newMessage = '';

            // 1. INSTANT OPTIMISTIC UI (Sat-Set 0ms langsung muncul di room chat)
            const tempId = 'temp_' + Date.now();
            const now = new Date();
            const timeStr = ('0' + now.getHours()).slice(-2) + ':' + ('0' + now.getMinutes()).slice(-2);
            
            const optimisticMsg = {
                id: tempId,
                id_sender: {{ auth()->id() }},
                sender_name: '{{ addslashes(auth()->user()->nama ?? 'Saya') }}',
                message: textToSend,
                is_me: true,
                is_read: false,
                created_at: now.toISOString(),
                time: timeStr,
                pending: true
            };

            this.messages.push(optimisticMsg);
            this.$nextTick(() => this.scrollToBottom());

            // 2. REAL-TIME THREAD LIST SNIPPET UPDATE & FLOAT TO TOP (Seperti WhatsApp)
            const threadInList = this.threads.find(t => t.id === this.activeThreadId);
            if (threadInList) {
                threadInList.last_message = textToSend;
                threadInList.last_message_time = timeStr;
                threadInList.last_message_iso = now.toISOString();

                // Pindahkan thread yang baru saja aktif ke posisi teratas daftar
                const currentIdx = this.threads.indexOf(threadInList);
                if (currentIdx > 0) {
                    this.threads.splice(currentIdx, 1);
                    this.threads.unshift(threadInList);
                }
            }

            // 3. Background Asynchronous Dispatch (Tanpa jeda/blocking UI)
            const tokenEl = document.querySelector('meta[name=csrf-token]');
            const csrfToken = tokenEl ? tokenEl.content : '';
            
            fetch('/chat/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    id_chat_booking: this.activeThreadId,
                    message: textToSend
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success && data.message) {
                    const idx = this.messages.findIndex(m => m.id === tempId);
                    if (idx !== -1) {
                        this.messages[idx] = data.message;
                    }
                }
            })
            .catch(err => {
                console.error('Client send message failed:', err);
            });
        },

        checkIsAtBottom() {
            const box = this.$refs.msgContainer;
            if (!box) return true;
            return box.scrollHeight - box.scrollTop - box.clientHeight < 100;
        },

        scrollToBottom() {
            const box = this.$refs.msgContainer;
            if (box) box.scrollTop = box.scrollHeight;
        }
    };
}
</script>
@endsection