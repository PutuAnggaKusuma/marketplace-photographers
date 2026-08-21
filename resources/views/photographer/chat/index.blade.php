@extends('layouts.photographer')

@section('title', 'Pesan & Chat Klien - Studio Dashboard')

@section('content')
<div class="space-y-4">
    <!-- Header Title -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <span class="text-[11px] font-extrabold uppercase tracking-widest text-amber-600 dark:text-amber-400">Dashboard Studio Fotografer</span>
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white tracking-tight">Pesan & Chat Klien</h1>
        </div>
    </div>

    <!-- Chat Container Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700/80 overflow-hidden grid grid-cols-1 lg:grid-cols-12 h-[calc(100vh-170px)] min-h-[560px]">
        
        <!-- Sidebar Thread List (4 Cols) -->
        <div class="lg:col-span-4 border-r border-gray-200 dark:border-gray-700/80 flex flex-col h-full bg-gray-50/50 dark:bg-gray-800/50 overflow-hidden">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700/80 shrink-0">
                <h2 class="text-sm font-extrabold text-gray-900 dark:text-white uppercase tracking-wider">Daftar Chat Klien</h2>
                <p class="text-xs text-gray-500 mt-0.5">Diskusi rencana & pertanyaan booking dari Klien</p>
            </div>

            <div class="flex-1 overflow-y-auto divide-y divide-gray-100 dark:divide-gray-700/60">
                @forelse($threads as $t)
                    @php
                        $isActive = $activeThread && $activeThread->id === $t->id;
                        $statusUser = $t->status_user ?? ['is_online' => false, 'label' => 'Aktif 7+ hari lalu', 'color' => 'text-gray-400', 'dot_color' => 'bg-gray-400'];
                        $unreadCount = $t->messages()->where('id_sender', '!=', auth()->id())->where('is_read', false)->count();
                    @endphp
                    <a href="{{ route('photographer.chat', ['thread' => $t->id]) }}" 
                       class="p-4 flex items-start gap-3.5 hover:bg-amber-50/60 dark:hover:bg-amber-950/30 transition block {{ $isActive ? 'bg-amber-50 dark:bg-amber-950/40 border-l-4 border-amber-500' : '' }}">
                        <div class="relative shrink-0">
                            <div class="w-11 h-11 rounded-full bg-gradient-to-tr from-amber-500 to-amber-300 flex items-center justify-center text-white font-black text-sm shadow-sm overflow-hidden">
                                {{ strtoupper(substr($t->client->nama ?? 'K', 0, 2)) }}
                            </div>
                            <span class="absolute bottom-0 right-0 w-3 h-3 rounded-full {{ $statusUser['dot_color'] }} border-2 border-white dark:border-gray-800"></span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2">
                                <h3 class="text-xs font-extrabold text-gray-900 dark:text-white truncate">{{ $t->client->nama ?? 'Klien' }}</h3>
                                <span class="text-[10px] text-gray-400 shrink-0">
                                    <span x-data="{ iso: '{{ $t->last_message_iso ?? '' }}', fallback: '{{ $t->last_message_time ?? '' }}' }" x-text="typeof formatLocalTime === 'function' ? formatLocalTime(iso, fallback) : fallback">{{ $t->last_message_time ?? '' }}</span>
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-1">
                                {{ $t->lastMessage ? $t->lastMessage->message : 'Belum ada pesan' }}
                            </p>
                        </div>
                        @if($unreadCount > 0)
                            <span class="ml-1 shrink-0 px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-500 text-white">
                                {{ $unreadCount }}
                            </span>
                        @endif
                    </a>
                @empty
                    <div class="p-8 text-center space-y-3">
                        <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-400 mx-auto flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                        </div>
                        <p class="text-xs font-medium text-gray-500">Belum ada pesan dari klien.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Main Chat Box (8 Cols) -->
        <div class="lg:col-span-8 flex flex-col h-full overflow-hidden bg-white dark:bg-gray-800"
             x-data="photographerChatApp({{ $activeThread ? $activeThread->id : 'null' }}, {{ Illuminate\Support\Js::from($formattedMessages) }}, {{ Illuminate\Support\Js::from($activeThread->status_user ?? ['is_online' => false, 'label' => 'Aktif 7+ hari lalu', 'color' => 'text-gray-400', 'dot_color' => 'bg-gray-400']) }})">

            @if($activeThread)
                <!-- Active Header with Dynamic Shopee-Style Last Seen Status -->
                <div class="p-4 sm:px-6 border-b border-gray-200 dark:border-gray-700/80 flex items-center justify-between bg-white dark:bg-gray-800 shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-amber-500 flex items-center justify-center text-white font-black text-xs shadow-sm">
                            {{ strtoupper(substr($activeThread->client->nama ?? 'K', 0, 2)) }}
                        </div>
                        <div>
                            <h3 class="text-sm font-extrabold text-gray-900 dark:text-white">{{ $activeThread->client->nama ?? 'Klien' }}</h3>
                            <p class="text-[11px] font-semibold flex items-center gap-1.5" :class="userStatus.color">
                                <span class="w-2 h-2 rounded-full shrink-0" :class="userStatus.dot_color"></span>
                                <span x-text="userStatus.label"></span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Messages -->
                <div x-ref="msgContainer" class="flex-1 p-4 sm:p-6 overflow-y-auto space-y-4 bg-gray-50/40 dark:bg-gray-900/40">
                    <template x-for="m in messages" :key="m.id">
                        <div :class="m.is_me ? 'flex justify-end' : 'flex justify-start'">
                            <div :class="m.is_me ? 'bg-amber-500 text-white rounded-2xl rounded-tr-none' : 'bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 border border-gray-200 dark:border-gray-700 rounded-2xl rounded-tl-none shadow-sm'"
                                 class="max-w-md px-4 py-3 space-y-1">
                                <div class="flex items-center justify-between gap-4 text-[10px]" :class="m.is_me ? 'text-amber-100' : 'text-gray-400'">
                                    <span class="font-bold" x-text="m.sender_name"></span>
                                    <span x-text="formatLocalTime(m.created_at || m.time, m.time)"></span>
                                </div>
                                <p class="text-xs leading-relaxed break-words whitespace-pre-line" x-text="m.message"></p>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Input Message Bar -->
                <div class="p-4 border-t border-gray-200 dark:border-gray-700/80 bg-white dark:bg-gray-800 shrink-0">
                    <form @submit.prevent="sendMessage()" class="flex items-center gap-3">
                        <input type="text" 
                               x-model="newMessage" 
                               @keydown.enter.prevent="sendMessage()"
                               placeholder="Tulis balasan Anda untuk klien..." 
                               class="flex-1 px-4 py-3 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-xs text-gray-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition">
                        <button type="submit" 
                                :disabled="!newMessage.trim() || isSending" 
                                class="px-6 py-3 rounded-xl bg-amber-500 hover:bg-amber-600 disabled:opacity-50 text-white font-extrabold text-xs shadow-md transition">
                            Kirim
                        </button>
                    </form>
                </div>
            @else
                <div class="flex-1 flex flex-col items-center justify-center p-8 text-center space-y-3">
                    <div class="w-16 h-16 rounded-full bg-amber-50 dark:bg-amber-950/60 text-amber-500 flex items-center justify-center">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Pilih Chat Klien</h3>
                    <p class="text-xs text-gray-500 max-w-sm">Pilih salah satu percakapan klien di sebelah kiri untuk membalas pesan.</p>
                </div>
            @endif

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

function photographerChatApp(activeThreadId, initialMessages, initialStatus) {
    return {
        activeThreadId: activeThreadId,
        messages: initialMessages || [],
        newMessage: '',
        isSending: false,
        userStatus: initialStatus || { is_online: false, label: 'Aktif 7+ hari lalu', color: 'text-gray-400', dot_color: 'bg-gray-400' },
        init() {
            if (this.activeThreadId) {
                this.fetchMessages();
                setInterval(() => this.fetchMessages(), 2500);
            }
        },
        fetchMessages() {
            if (!this.activeThreadId) return;
            fetch('/photographer/chat/messages/' + this.activeThreadId)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        if (data.other_status) {
                            this.userStatus = data.other_status;
                        }
                        const isAtBottom = this.checkIsAtBottom();
                        const prevCount = this.messages.length;
                        this.messages = data.messages;
                        if (data.messages.length > prevCount || isAtBottom) {
                            this.$nextTick(() => this.scrollToBottom());
                        }
                    }
                })
                .catch(err => console.log('Chat fetch error:', err));
        },
        sendMessage() {
            if (!this.newMessage.trim() || this.isSending || !this.activeThreadId) return;
            const textToSend = this.newMessage.trim();
            this.isSending = true;
            this.newMessage = '';

            const tokenEl = document.querySelector('meta[name=csrf-token]');
            const csrfToken = tokenEl ? tokenEl.content : '';
            
            fetch('/photographer/chat/send', {
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
                this.isSending = false;
                if (data.success) {
                    this.fetchMessages();
                    this.$nextTick(() => this.scrollToBottom());
                }
            })
            .catch(() => {
                this.isSending = false;
                this.newMessage = textToSend;
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
        },
        formatLocalTime(isoStr, fallbackTime) {
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
        }
    };
}
</script>
@endsection