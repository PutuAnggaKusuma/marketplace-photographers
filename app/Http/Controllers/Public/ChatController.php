<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ChatBooking;
use App\Models\ChatMessage;
use App\Models\RoleClient;
use App\Models\RolePhotographer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Helper Format Real-Time Last Seen Status WhatsApp.
     */
    public static function formatLastSeen($lastSeenAt)
    {
        if (!$lastSeenAt) {
            return [
                'is_online' => false,
                'label' => 'Aktif 7+ hari lalu',
                'color' => 'text-gray-500 dark:text-gray-400',
                'dot_color' => 'bg-gray-400',
            ];
        }

        $lastSeen = Carbon::parse($lastSeenAt);
        $diffSeconds = $lastSeen->diffInSeconds(now());

        if ($diffSeconds <= 120) {
            return [
                'is_online' => true,
                'label' => 'Online',
                'color' => 'text-emerald-700 dark:text-emerald-300 font-extrabold',
                'dot_color' => 'bg-emerald-600 animate-pulse',
            ];
        }

        $intMinutes = (int) $lastSeen->diffInMinutes(now());
        $intHours = (int) $lastSeen->diffInHours(now());
        $intDays = (int) $lastSeen->diffInDays(now());

        if ($intDays >= 7) {
            $label = 'Aktif 7+ hari lalu';
        } elseif ($intDays >= 1) {
            $label = "Aktif {$intDays} hari lalu";
        } elseif ($intHours >= 1) {
            $label = "Aktif {$intHours} jam lalu";
        } else {
            $label = "Aktif {$intMinutes} menit lalu";
        }

        return [
            'is_online' => false,
            'label' => $label,
            'color' => 'text-gray-500 dark:text-gray-400',
            'dot_color' => 'bg-gray-400',
        ];
    }

    /**
     * Helper URL Avatar (Mendukung URL eksternal Unsplash/Cloud dan storage lokal).
     */
    public static function resolveAvatarUrl($foto)
    {
        if (!$foto) return null;
        return str_starts_with($foto, 'http') ? $foto : asset('storage/' . $foto);
    }

    /**
     * Format & Urutkan Thread Chat Berdasarkan Pesan / Aktivitas Terbaru Teratas (Newest on Top).
     */
    public static function formatThreadsForUser($rawThreads, $user, $role)
    {
        return $rawThreads->sortByDesc(function ($t) {
            if ($t->lastMessage && $t->lastMessage->created_at) {
                return $t->lastMessage->created_at->timestamp;
            }
            return $t->updated_at ? $t->updated_at->timestamp : 0;
        })->values()->map(function ($t) use ($user, $role) {
            if ($role === 'photographer') {
                $otherUser = $t->client->user ?? null;
                $name = $t->client->nama ?? 'Klien';
                $initial = strtoupper(substr($name, 0, 2));
                $foto = self::resolveAvatarUrl($t->client->foto ?? null);
                $photographerId = null;
                $clientId = $t->client->id ?? null;
                $userId = $t->client->id_user ?? ($otherUser ? $otherUser->id : null);
            } else {
                $otherUser = $t->photographer->user ?? null;
                $name = $t->photographer->nama ?? 'Fotografer';
                $initial = strtoupper(substr($name, 0, 2));
                $foto = self::resolveAvatarUrl($t->photographer->foto ?? null);
                $photographerId = $t->photographer->id ?? null;
                $clientId = null;
                $userId = $t->photographer->id_user ?? ($otherUser ? $otherUser->id : null);
            }

            $status_user = self::formatLastSeen($otherUser ? $otherUser->last_seen_at : null);

            $lastMessageTime = '';
            $lastMessageIso = '';
            $lastTimestamp = 0;

            if ($t->lastMessage && $t->lastMessage->created_at) {
                $lastMessageTime = $t->lastMessage->created_at->setTimezone('Asia/Jakarta')->format('H:i');
                $lastMessageIso = $t->lastMessage->created_at->toIso8601String();
                $lastTimestamp = $t->lastMessage->created_at->timestamp;
            } elseif ($t->updated_at) {
                $lastMessageTime = $t->updated_at->setTimezone('Asia/Jakarta')->format('H:i');
                $lastMessageIso = $t->updated_at->toIso8601String();
                $lastTimestamp = $t->updated_at->timestamp;
            }

            $lastMessageText = 'Belum ada pesan';
            if ($t->lastMessage) {
                if ($t->lastMessage->deleted_for_all) {
                    $lastMessageText = '[Pesan ini telah dihapus]';
                } elseif ($t->lastMessage->attachment_url && empty(trim($t->lastMessage->message ?? ''))) {
                    $lastMessageText = '[Foto]';
                } elseif ($t->lastMessage->attachment_url) {
                    $lastMessageText = '[Foto] ' . $t->lastMessage->message;
                } else {
                    $lastMessageText = $t->lastMessage->message;
                }
            }

            return [
                'id' => $t->id,
                'name' => $name,
                'initial' => $initial,
                'foto' => $foto,
                'photographer_id' => $photographerId,
                'client_id' => $clientId,
                'user_id' => $userId,
                'last_message' => $lastMessageText,
                'last_message_time' => $lastMessageTime,
                'last_message_iso' => $lastMessageIso,
                'last_timestamp' => $lastTimestamp,
                'unread_count' => $t->messages()->where('id_sender', '!=', $user->id)->where('is_read', false)->count(),
                'status_user' => $status_user,
            ];
        });
    }

    /**
     * Helper Format Item Pesan Chat (Mendukung Edit, Hapus, Quote Reply & Foto ala WhatsApp).
     */
    protected static function formatMessageItem($msg, $user)
    {
        $isMe = (int) $msg->id_sender === (int) $user->id;
        $deletedForAll = (bool) $msg->deleted_for_all;

        // WA Standard: Edit maksimal 15 menit jika bukan pesan yang dihapus
        $canEdit = $isMe && !$deletedForAll && $msg->created_at && $msg->created_at->diffInMinutes(now()) <= 15;
        $canDeleteEveryone = $isMe && !$deletedForAll;

        // Quote Reply Data
        $replyToData = null;
        if ($msg->replyTo && !$deletedForAll) {
            $replySenderName = $msg->replyTo->sender ? $msg->replyTo->sender->nama : 'Pengguna';
            $replyToData = [
                'id' => $msg->replyTo->id,
                'id_sender' => $msg->replyTo->id_sender,
                'sender_name' => $replySenderName,
                'message' => $msg->replyTo->deleted_for_all ? '[Pesan ini telah dihapus]' : \Illuminate\Support\Str::limit($msg->replyTo->message, 90),
                'attachment_url' => $msg->replyTo->attachment_url,
            ];
        }

        // Custom Offer Data (Penawaran Khusus Chat)
        $customOfferData = null;
        if ($msg->customOffer && !$deletedForAll) {
            $offer = $msg->customOffer;
            $customOfferData = [
                'id' => $offer->id,
                'judul_penawaran' => $offer->judul_penawaran,
                'deskripsi' => $offer->deskripsi,
                'booking_date' => $offer->booking_date ? \Carbon\Carbon::parse($offer->booking_date)->translatedFormat('d F Y') : '-',
                'booking_time' => $offer->booking_time ? substr($offer->booking_time, 0, 5) . ' WIB' : '-',
                'durasi_jam' => $offer->durasi_jam,
                'lokasi' => $offer->lokasi,
                'harga' => (float) $offer->harga,
                'formatted_harga' => 'Rp ' . number_format($offer->harga, 0, ',', '.'),
                'status' => $offer->status,
                'id_contract' => $offer->id_contract,
                'invoice_url' => $offer->id_contract ? route('booking.invoice', $offer->id_contract) : null,
            ];
        }

        return [
            'id' => $msg->id,
            'id_sender' => $msg->id_sender,
            'sender_name' => $msg->sender->nama ?? 'Pengguna',
            'message' => $deletedForAll ? 'Pesan ini telah dihapus' : $msg->message,
            'reply_to' => $replyToData,
            'custom_offer' => $customOfferData,
            'attachment_url' => $deletedForAll ? null : $msg->attachment_url,
            'attachment_type' => $deletedForAll ? null : ($msg->attachment_type ?? 'image'),
            'is_me' => $isMe,
            'is_read' => (bool) $msg->is_read,
            'is_edited' => (bool) $msg->is_edited,
            'edited_at' => $msg->edited_at ? $msg->edited_at->toIso8601String() : null,
            'deleted_for_all' => $deletedForAll,
            'can_edit' => $canEdit,
            'can_delete_everyone' => $canDeleteEveryone,
            'created_at' => $msg->created_at ? $msg->created_at->toIso8601String() : null,
            'time' => $msg->created_at ? $msg->created_at->format('H:i') : '',
        ];
    }

    /**
     * Tampilkan Halaman Chat Client.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $user->update(['last_seen_at' => now()]);

        $client = RoleClient::firstOrCreate(['id_user' => $user->id], [
            'nama' => $user->nama,
            'nomor_telepon' => '081234567890',
            'alamat' => 'Jakarta, Indonesia',
        ]);

        $activeThreadId = $request->query('thread');
        $photographerIdParam = $request->query('photographer') ?? $request->query('photographer_id');

        if ($photographerIdParam) {
            $threadByPhoto = ChatBooking::firstOrCreate([
                'id_client' => $client->id,
                'id_photographer' => $photographerIdParam,
            ]);
            $activeThreadId = $threadByPhoto->id;
        } elseif ($activeThreadId) {
            // Check if activeThreadId belongs to a ChatBooking
            $exists = ChatBooking::where('id_client', $client->id)->where('id', $activeThreadId)->exists();
            if (!$exists) {
                // Fallback: check if it was passed as a contract ID
                $contract = \App\Models\Contract::where('id', $activeThreadId)->first();
                if ($contract && $contract->id_photographer) {
                    $threadByContract = ChatBooking::firstOrCreate([
                        'id_client' => $client->id,
                        'id_photographer' => $contract->id_photographer,
                    ]);
                    $activeThreadId = $threadByContract->id;
                }
            }
        }

        $rawThreads = ChatBooking::where('id_client', $client->id)
            ->with(['photographer.user', 'lastMessage'])
            ->get();

        $formattedThreads = self::formatThreadsForUser($rawThreads, $user, 'client');
        $threads = $formattedThreads;

        $activeThread = null;
        $messages = collect();

        if ($activeThreadId) {
            $activeThreadModel = ChatBooking::where('id_client', $client->id)
                ->where('id', $activeThreadId)
                ->with(['photographer.user'])
                ->first();

            if ($activeThreadModel) {
                // Mark unread messages from photographer as read
                ChatMessage::where('id_chat_booking', $activeThreadModel->id)
                    ->where('id_sender', '!=', $user->id)
                    ->where('is_read', false)
                    ->update(['is_read' => true]);

                $photoUser = $activeThreadModel->photographer->user ?? null;
                $activeThread = (object) [
                    'id' => $activeThreadModel->id,
                    'name' => $activeThreadModel->photographer->nama ?? 'Fotografer',
                    'initial' => strtoupper(substr($activeThreadModel->photographer->nama ?? 'F', 0, 2)),
                    'foto' => self::resolveAvatarUrl($activeThreadModel->photographer->foto ?? null),
                    'photographer_id' => $activeThreadModel->photographer->id ?? null,
                    'client_id' => null,
                    'user_id' => $activeThreadModel->photographer->id_user ?? ($photoUser ? $photoUser->id : null),
                    'status_user' => self::formatLastSeen($photoUser ? $photoUser->last_seen_at : null),
                ];

                $messages = ChatMessage::where('id_chat_booking', $activeThreadModel->id)
                    ->with(['sender', 'replyTo.sender', 'customOffer'])
                    ->orderBy('created_at', 'asc')
                    ->get();
            }
        }

        $formattedMessages = $messages->filter(function ($msg) use ($user) {
            $deletedBy = $msg->deleted_by_users ?? [];
            return !in_array($user->id, $deletedBy);
        })->map(function ($msg) use ($user) {
            return self::formatMessageItem($msg, $user);
        })->values();

        return view('public.chat.index', compact('threads', 'formattedThreads', 'activeThread', 'formattedMessages'));
    }

    /**
     * Tampilkan Halaman Chat Fotografer.
     */
    public function photographerIndex(Request $request)
    {
        $user = Auth::user();
        $user->update(['last_seen_at' => now()]);

        $photographer = RolePhotographer::where('id_user', $user->id)->first();
        if (!$photographer) {
            $photographer = RolePhotographer::first();
        }

        $rawThreads = ChatBooking::where('id_photographer', $photographer->id)
            ->with(['client.user', 'lastMessage'])
            ->get();

        $formattedThreads = self::formatThreadsForUser($rawThreads, $user, 'photographer');
        $threads = $formattedThreads;

        $activeThreadId = $request->query('thread');

        $activeThread = null;
        $messages = collect();

        if ($activeThreadId) {
            $activeThreadModel = ChatBooking::where('id_photographer', $photographer->id)
                ->where('id', $activeThreadId)
                ->with(['client.user'])
                ->first();

            if ($activeThreadModel) {
                // Mark unread messages from client as read
                ChatMessage::where('id_chat_booking', $activeThreadModel->id)
                    ->where('id_sender', '!=', $user->id)
                    ->where('is_read', false)
                    ->update(['is_read' => true]);

                $clientUser = $activeThreadModel->client->user ?? null;
                $activeThread = (object) [
                    'id' => $activeThreadModel->id,
                    'name' => $activeThreadModel->client->nama ?? 'Klien',
                    'initial' => strtoupper(substr($activeThreadModel->client->nama ?? 'K', 0, 2)),
                    'foto' => self::resolveAvatarUrl($activeThreadModel->client->foto ?? null),
                    'photographer_id' => null,
                    'client_id' => $activeThreadModel->client->id ?? null,
                    'user_id' => $activeThreadModel->client->id_user ?? ($clientUser ? $clientUser->id : null),
                    'status_user' => self::formatLastSeen($clientUser ? $clientUser->last_seen_at : null),
                ];

                $messages = ChatMessage::where('id_chat_booking', $activeThreadModel->id)
                    ->with(['sender', 'replyTo.sender', 'customOffer'])
                    ->orderBy('created_at', 'asc')
                    ->get();
            }
        }

        $formattedMessages = $messages->filter(function ($msg) use ($user) {
            $deletedBy = $msg->deleted_by_users ?? [];
            return !in_array($user->id, $deletedBy);
        })->map(function ($msg) use ($user) {
            return self::formatMessageItem($msg, $user);
        })->values();

        return view('photographer.chat.index', compact('threads', 'formattedThreads', 'activeThread', 'formattedMessages'));
    }

    /**
     * Memulai atau Membuka Thread Chat dari Fotografer ke Klien Spesifik.
     */
    public function photographerStartChat($clientId)
    {
        $user = Auth::user();
        $photographer = RolePhotographer::where('id_user', $user->id)->first();
        if (!$photographer) {
            $photographer = RolePhotographer::first();
        }

        $client = RoleClient::findOrFail($clientId);

        $thread = ChatBooking::firstOrCreate([
            'id_client' => $client->id,
            'id_photographer' => $photographer->id,
        ]);

        return redirect()->route('photographer.chat', ['thread' => $thread->id]);
    }

    /**
     * Mulai Chat dari Halaman Profil Fotografer / Tombol Kontak.
     */
    public function startChat($photographerId)
    {
        $user = Auth::user();
        $photographer = RolePhotographer::findOrFail($photographerId);

        $client = RoleClient::firstOrCreate(['id_user' => $user->id], [
            'nama' => $user->nama,
            'nomor_telepon' => '081234567890',
            'alamat' => 'Jakarta, Indonesia',
        ]);

        $thread = ChatBooking::firstOrCreate([
            'id_client' => $client->id,
            'id_photographer' => $photographer->id,
        ]);

        if ($thread->messages()->count() === 0) {
            ChatMessage::create([
                'id_chat_booking' => $thread->id,
                'id_sender' => $photographer->id_user ?? $user->id,
                'message' => 'Halo Kak ' . $user->nama . '! Ada yang bisa kami bantu mengenai rencana sesi foto Anda bersama ' . $photographer->nama . '?',
                'is_read' => false,
            ]);
            $thread->touch();
        }

        if ($user->role === 'photographer') {
            return redirect()->route('photographer.chat', ['thread' => $thread->id]);
        }

        return redirect()->route('client.chat', ['thread' => $thread->id]);
    }

    /**
     * Ambil Pesan Terbaru via JSON (Dipakai oleh Alpine.js Polling & SPA Switch).
     */
    public function getMessages($chatBookingId)
    {
        $user = Auth::user();
        $user->update(['last_seen_at' => now()]);

        $thread = ChatBooking::findOrFail($chatBookingId);

        // Mark unread messages as read
        ChatMessage::where('id_chat_booking', $thread->id)
            ->where('id_sender', '!=', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $otherUser = null;
        $otherName = '';
        $otherAvatar = '';
        $otherInitial = '';
        $photographerId = null;
        $clientId = null;
        $userId = null;

        if ($user->role === 'photographer') {
            $otherUser = $thread->client->user ?? null;
            $otherName = $thread->client->nama ?? 'Klien';
            $otherAvatar = self::resolveAvatarUrl($thread->client->foto ?? null);
            $otherInitial = strtoupper(substr($otherName, 0, 2));
            $clientId = $thread->client->id ?? null;
            $userId = $thread->client->id_user ?? ($otherUser ? $otherUser->id : null);
        } else {
            $otherUser = $thread->photographer->user ?? null;
            $otherName = $thread->photographer->nama ?? 'Fotografer';
            $otherAvatar = self::resolveAvatarUrl($thread->photographer->foto ?? null);
            $otherInitial = strtoupper(substr($otherName, 0, 2));
            $photographerId = $thread->photographer->id ?? null;
            $userId = $thread->photographer->id_user ?? ($otherUser ? $otherUser->id : null);
        }

        $otherStatus = self::formatLastSeen($otherUser ? $otherUser->last_seen_at : null);
        $otherUserId = $otherUser ? $otherUser->id : null;
        $otherIsTyping = $otherUserId ? \Illuminate\Support\Facades\Cache::has("chat_typing_{$thread->id}_{$otherUserId}") : false;

        $messages = ChatMessage::where('id_chat_booking', $thread->id)
            ->with(['sender', 'replyTo.sender', 'customOffer'])
            ->orderBy('created_at', 'asc')
            ->get()
            ->filter(function ($msg) use ($user) {
                $deletedBy = $msg->deleted_by_users ?? [];
                return !in_array($user->id, $deletedBy);
            })
            ->map(function ($msg) use ($user) {
                return self::formatMessageItem($msg, $user);
            })
            ->values();

        // Muat daftar threads terupdate untuk sinkronisasi real-time sidebar
        if ($user->role === 'photographer') {
            $photographer = RolePhotographer::where('id_user', $user->id)->first();
            $rawThreads = $photographer ? ChatBooking::where('id_photographer', $photographer->id)->with(['client.user', 'lastMessage'])->get() : collect();
            $formattedThreads = self::formatThreadsForUser($rawThreads, $user, 'photographer');
        } else {
            $client = RoleClient::where('id_user', $user->id)->first();
            $rawThreads = $client ? ChatBooking::where('id_client', $client->id)->with(['photographer.user', 'lastMessage'])->get() : collect();
            $formattedThreads = self::formatThreadsForUser($rawThreads, $user, 'client');
        }

        return response()->json([
            'success' => true,
            'thread_id' => $thread->id,
            'other_name' => $otherName,
            'other_avatar' => $otherAvatar,
            'other_initial' => $otherInitial,
            'photographer_id' => $photographerId,
            'client_id' => $clientId,
            'user_id' => $userId,
            'other_status' => $otherStatus,
            'other_is_typing' => $otherIsTyping,
            'messages' => $messages,
            'threads' => $formattedThreads,
        ]);
    }

    /**
     * Set Status Sedang Mengetik (Typing Heartbeat).
     */
    public function setTyping(Request $request)
    {
        $request->validate([
            'id_chat_booking' => ['required', 'exists:chat_bookings,id'],
        ]);

        $user = Auth::user();
        \Illuminate\Support\Facades\Cache::put("chat_typing_{$request->id_chat_booking}_{$user->id}", true, now()->addSeconds(4));

        return response()->json(['success' => true]);
    }

    /**
     * Kirim Pesan Baru ke Thread Chat (Mendukung Teks, Quote Reply, dan Foto Referensi).
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'id_chat_booking' => ['required', 'exists:chat_bookings,id'],
            'message' => ['nullable', 'string', 'max:2000'],
            'reply_to_id' => ['nullable', 'exists:chat_messages,id'],
            'attachment' => ['nullable', 'file', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ]);

        if (empty(trim($request->message ?? '')) && !$request->hasFile('attachment')) {
            return response()->json(['success' => false, 'message' => 'Pesan atau foto tidak boleh kosong.'], 422);
        }

        $user = Auth::user();
        $user->update(['last_seen_at' => now()]);

        $thread = ChatBooking::findOrFail($request->id_chat_booking);

        $isClient = $thread->client && (int) $thread->client->id_user === (int) $user->id;
        $isPhotographer = $thread->photographer && (int) $thread->photographer->id_user === (int) $user->id;
        if (!$isClient && !$isPhotographer && !in_array($user->role, ['admin', 'super_admin'])) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses ke percakapan ini.'], 403);
        }

        $attachmentUrl = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = 'chat_' . time() . '_' . \Illuminate\Support\Str::random(10) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('chat_attachments', $filename, 'public');
            $attachmentUrl = asset('storage/' . $path);
        }

        $msg = ChatMessage::create([
            'id_chat_booking' => $thread->id,
            'id_sender' => $user->id,
            'reply_to_id' => $request->reply_to_id,
            'message' => $request->message ?? '',
            'attachment_url' => $attachmentUrl,
            'attachment_type' => $attachmentUrl ? 'image' : null,
            'is_read' => false,
        ]);

        $thread->touch();

        \Illuminate\Support\Facades\Cache::forget("chat_typing_{$thread->id}_{$user->id}");

        // Broadcast Real-Time via Laravel Reverb WebSocket
        try {
            broadcast(new \App\Events\MessageSent($msg->fresh(['sender', 'replyTo.sender']), $user))->toOthers();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Reverb broadcast note: ' . $e->getMessage());
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => self::formatMessageItem($msg->fresh(['sender', 'replyTo.sender']), $user),
            ]);
        }

        return redirect()->back();
    }

    /**
     * Edit Pesan Chat (Maksimal 15 Menit sejak dikirim).
     */
    public function updateMessage(Request $request, $id)
    {
        $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $user = Auth::user();
        $user->update(['last_seen_at' => now()]);

        $msg = ChatMessage::findOrFail($id);

        if ((int) $msg->id_sender !== (int) $user->id) {
            return response()->json(['success' => false, 'message' => 'Anda tidak berhak mengedit pesan ini.'], 403);
        }

        if ($msg->deleted_for_all) {
            return response()->json(['success' => false, 'message' => 'Pesan yang sudah dihapus tidak dapat diedit.'], 400);
        }

        if ($msg->created_at && $msg->created_at->diffInMinutes(now()) > 15) {
            return response()->json(['success' => false, 'message' => 'Batas waktu pengeditan pesan (15 menit) telah berakhir.'], 400);
        }

        $msg->update([
            'message' => $request->message,
            'is_edited' => true,
            'edited_at' => now(),
        ]);

        $msg->chatBooking->touch();

        return response()->json([
            'success' => true,
            'message' => self::formatMessageItem($msg->fresh(), $user),
        ]);
    }

    /**
     * Hapus Pesan Chat (Hapus untuk Semua Orang / Hapus untuk Saya).
     */
    public function deleteMessage(Request $request, $id)
    {
        $user = Auth::user();
        $user->update(['last_seen_at' => now()]);

        $msg = ChatMessage::findOrFail($id);
        $mode = $request->input('mode', 'for_everyone'); // 'for_everyone' or 'for_me'

        $thread = $msg->chatBooking;
        $isClient = $thread->client && (int) $thread->client->id_user === (int) $user->id;
        $isPhotographer = $thread->photographer && (int) $thread->photographer->id_user === (int) $user->id;
        if (!$isClient && !$isPhotographer && $user->role !== 'super_admin') {
            return response()->json(['success' => false, 'message' => 'Anda bukan peserta percakapan ini.'], 403);
        }

        if ($mode === 'for_everyone') {
            if ((int) $msg->id_sender !== (int) $user->id) {
                return response()->json(['success' => false, 'message' => 'Hanya pengirim yang dapat menarik pesan untuk semua orang.'], 403);
            }
            $msg->update([
                'deleted_for_all' => true,
                'message' => 'Pesan ini telah dihapus',
            ]);
        } else {
            // for_me: append to deleted_by_users array
            $deletedBy = $msg->deleted_by_users ?? [];
            if (!in_array($user->id, $deletedBy)) {
                $deletedBy[] = $user->id;
                $msg->update(['deleted_by_users' => $deletedBy]);
            }
        }

        $thread->touch();

        return response()->json([
            'success' => true,
            'mode' => $mode,
            'message' => self::formatMessageItem($msg->fresh(), $user),
        ]);
    }

    /**
     * Endpoint API Real-Time Unread Chat Count.
     */
    public function getUnreadCount()
    {
        if (!Auth::check()) {
            return response()->json(['unread_count' => 0]);
        }

        $user = Auth::user();
        $unreadCount = 0;

        if ($user->role === 'client') {
            $client = RoleClient::where('id_user', $user->id)->first();
            if ($client) {
                $threadIds = ChatBooking::where('id_client', $client->id)->pluck('id');
                $unreadCount = ChatMessage::whereIn('id_chat_booking', $threadIds)
                    ->where('id_sender', '!=', $user->id)
                    ->where('is_read', false)
                    ->count();
            }
        } elseif ($user->role === 'photographer') {
            $photographer = RolePhotographer::where('id_user', $user->id)->first();
            if ($photographer) {
                $threadIds = ChatBooking::where('id_photographer', $photographer->id)->pluck('id');
                $unreadCount = ChatMessage::whereIn('id_chat_booking', $threadIds)
                    ->where('id_sender', '!=', $user->id)
                    ->where('is_read', false)
                    ->count();
            }
        }

        return response()->json([
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Kirim Penawaran Khusus via Chat (Custom Offer / Quote oleh Fotografer).
     */
    public function storeCustomOffer(Request $request)
    {
        $request->validate([
            'id_chat_booking' => ['required', 'exists:chat_bookings,id'],
            'judul_penawaran' => ['required', 'string', 'min:3', 'max:200'],
            'booking_date' => ['required', 'date', 'after_or_equal:today'],
            'booking_time' => ['required'],
            'durasi_jam' => ['required', 'integer', 'min:1', 'max:48'],
            'lokasi' => ['required', 'string', 'max:255'],
            'harga' => ['required', 'numeric', 'min:50000'],
            'deskripsi' => ['required', 'string', 'max:2000'],
        ], [
            'judul_penawaran.required' => 'Judul paket penawaran khusus wajib diisi.',
            'booking_date.required' => 'Tanggal rencana sesi pemotretan wajib ditentukan.',
            'booking_date.after_or_equal' => 'Tanggal sesi minimal hari ini atau mendatang.',
            'booking_time.required' => 'Jam mulai pemotretan wajib ditentukan.',
            'durasi_jam.required' => 'Estimasi durasi sesi wajib ditentukan.',
            'lokasi.required' => 'Lokasi pemotretan wajib diisi.',
            'harga.required' => 'Total tarif penawaran khusus wajib diisi.',
            'harga.min' => 'Tarif penawaran khusus minimal Rp 50.000.',
            'deskripsi.required' => 'Rincian fasilitas dan ketentuan penawaran wajib dijelaskan.',
        ]);

        $user = Auth::user();
        $thread = ChatBooking::with(['client.user'])->findOrFail($request->id_chat_booking);

        $photographer = RolePhotographer::where('id_user', $user->id)->first();
        if (!$photographer || $thread->id_photographer !== $photographer->id) {
            return response()->json(['success' => false, 'message' => 'Hanya fotografer pemilik obrolan yang dapat membuat penawaran khusus.'], 403);
        }

        $offer = \App\Models\ChatCustomOffer::create([
            'id_chat_booking' => $thread->id,
            'id_photographer' => $photographer->id,
            'id_client' => $thread->id_client,
            'judul_penawaran' => trim($request->judul_penawaran),
            'deskripsi' => trim($request->deskripsi),
            'booking_date' => $request->booking_date,
            'booking_time' => $request->booking_time,
            'durasi_jam' => (int) $request->durasi_jam,
            'lokasi' => trim($request->lokasi),
            'harga' => $request->harga,
            'status' => 'pending',
        ]);

        $formattedHarga = 'Rp ' . number_format($offer->harga, 0, ',', '.');
        $msgText = "Mengirimkan Penawaran Khusus: {$offer->judul_penawaran} — {$formattedHarga}";

        $msg = ChatMessage::create([
            'id_chat_booking' => $thread->id,
            'id_sender' => $user->id,
            'id_custom_offer' => $offer->id,
            'message' => $msgText,
            'attachment_url' => null,
            'attachment_type' => 'custom_offer',
            'is_read' => false,
        ]);

        $thread->touch();

        // Notifikasi In-App ke Klien
        if ($thread->client && $thread->client->id_user) {
            \App\Models\Notification::send(
                $thread->client->id_user,
                'Penawaran Khusus Diterima',
                "Fotografer {$photographer->nama} mengirimkan penawaran khusus '{$offer->judul_penawaran}' seharga {$formattedHarga}.",
                'custom_offer',
                route('client.chat', ['thread' => $thread->id])
            );
        }

        \App\Models\ActivityLog::record(
            'custom_offer_sent',
            "Fotografer {$photographer->nama} mengirimkan penawaran khusus '{$offer->judul_penawaran}' seharga {$formattedHarga} ke klien {$thread->client->nama}",
            $offer
        );

        return response()->json([
            'success' => true,
            'message' => 'Penawaran khusus berhasil dikirimkan ke obrolan klien!',
            'offer_id' => $offer->id,
            'chat_message' => self::formatMessageItem($msg->fresh(['sender', 'customOffer']), $user),
        ]);
    }

    /**
     * Setujui Penawaran Khusus oleh Klien & Terbitkan Kontrak Invoice.
     */
    public function acceptCustomOffer(Request $request, $id)
    {
        $user = Auth::user();
        $offer = \App\Models\ChatCustomOffer::with(['chatBooking', 'photographer.user', 'client'])->findOrFail($id);

        $client = RoleClient::where('id_user', $user->id)->first();
        if (!$client || $offer->id_client !== $client->id) {
            return response()->json(['success' => false, 'message' => 'Hanya klien penerima yang berhak menyetujui penawaran ini.'], 403);
        }

        if ($offer->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Penawaran ini sudah diproses sebelumnya.'], 400);
        }

        $feeMarketplace = $offer->harga * 0.10;
        $netPayout = $offer->harga - $feeMarketplace;

        // 1. Buat Contract Resmi
        $contract = \App\Models\Contract::create([
            'id_photographer' => $offer->id_photographer,
            'id_client' => $offer->id_client,
            'jumlah' => $offer->harga,
            'payment_type' => 'full',
            'fee_marketplace' => $feeMarketplace,
            'payout_amount' => $netPayout,
            'status_contract' => 'pending_payment',
            'status_payout' => 'holding',
            'is_validated_client' => true,
            'is_validated_photographer' => true,
            'payout_notes' => 'Pemesanan Penawaran Khusus Chat: ' . $offer->judul_penawaran,
        ]);

        // 2. Buat ContractBooking Detail
        \App\Models\ContractBooking::create([
            'id_contract' => $contract->id,
            'id_service' => null,
            'booking_date' => $offer->booking_date,
            'jam_mulai' => $offer->booking_time,
            'jam_selesai' => date('H:i', strtotime($offer->booking_time . ' +' . ($offer->durasi_jam ?: 4) . ' hours')),
            'durasi' => "{$offer->durasi_jam} Jam",
            'lokasi' => $offer->lokasi,
            'catatan_khusus' => "Penawaran Khusus Chat: {$offer->judul_penawaran}. " . $offer->deskripsi,
            'status_booking' => 'pending',
        ]);

        // 3. Buat Payment Record Awal
        \App\Models\Payment::create([
            'id_contract' => $contract->id,
            'payment_amount' => $offer->harga,
            'payment_status' => 'pending',
            'contract_payment_type' => 'full',
            'payment_type' => 'transfer',
            'external_id' => 'INV-OFR-' . date('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(6)),
        ]);

        // 4. Update Status Penawaran Khusus
        $offer->update([
            'status' => 'accepted',
            'id_contract' => $contract->id,
        ]);

        $contractCode = '#KTRK-' . str_pad($contract->id, 5, '0', STR_PAD_LEFT);

        // 5. Kirim Pesan Konfirmasi di Chat
        ChatMessage::create([
            'id_chat_booking' => $offer->id_chat_booking,
            'id_sender' => $user->id,
            'message' => "Saya telah menyetujui penawaran khusus '{$offer->judul_penawaran}' ({$contractCode}). Menuju pembayaran invoice...",
            'is_read' => false,
        ]);

        $offer->chatBooking->touch();

        // 6. Notifikasi ke Fotografer
        if ($offer->photographer && $offer->photographer->id_user) {
            \App\Models\Notification::send(
                $offer->photographer->id_user,
                'Penawaran Khusus Disetujui',
                "Klien {$client->nama} telah menyetujui penawaran khusus '{$offer->judul_penawaran}' ({$contractCode}). Klien diarahkan untuk melakukan pembayaran transfer.",
                'custom_offer_accepted',
                route('photographer.bookings')
            );
        }

        \App\Models\ActivityLog::record(
            'custom_offer_accepted',
            "Klien {$client->nama} menyetujui penawaran khusus '{$offer->judul_penawaran}' ({$contractCode})",
            $contract
        );

        $invoiceUrl = route('booking.invoice', $contract->id);

        return response()->json([
            'success' => true,
            'message' => 'Penawaran berhasil disetujui! Mengarahkan ke halaman pembayaran...',
            'invoice_url' => $invoiceUrl,
            'redirect_url' => $invoiceUrl,
        ]);
    }

    /**
     * Tolak Penawaran Khusus oleh Klien.
     */
    public function rejectCustomOffer(Request $request, $id)
    {
        $user = Auth::user();
        $offer = \App\Models\ChatCustomOffer::with(['chatBooking', 'photographer.user', 'client'])->findOrFail($id);

        $client = RoleClient::where('id_user', $user->id)->first();
        if (!$client || $offer->id_client !== $client->id) {
            return response()->json(['success' => false, 'message' => 'Hanya klien penerima yang berhak menolak penawaran ini.'], 403);
        }

        if ($offer->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Penawaran ini sudah diproses sebelumnya.'], 400);
        }

        $offer->update(['status' => 'rejected']);

        ChatMessage::create([
            'id_chat_booking' => $offer->id_chat_booking,
            'id_sender' => $user->id,
            'message' => "Mohon maaf, saya belum dapat menyetujui penawaran khusus '{$offer->judul_penawaran}' saat ini.",
            'is_read' => false,
        ]);

        $offer->chatBooking->touch();

        if ($offer->photographer && $offer->photographer->id_user) {
            \App\Models\Notification::send(
                $offer->photographer->id_user,
                'Penawaran Khusus Ditolak',
                "Klien {$client->nama} menolak penawaran khusus '{$offer->judul_penawaran}'.",
                'custom_offer_rejected',
                route('photographer.chat', ['thread' => $offer->id_chat_booking])
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Penawaran khusus ditolak.',
        ]);
    }
}