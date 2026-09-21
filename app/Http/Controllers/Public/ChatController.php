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
            } else {
                $otherUser = $t->photographer->user ?? null;
                $name = $t->photographer->nama ?? 'Fotografer';
                $initial = strtoupper(substr($name, 0, 2));
                $foto = self::resolveAvatarUrl($t->photographer->foto ?? null);
                $photographerId = $t->photographer->id ?? null;
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

            return [
                'id' => $t->id,
                'name' => $name,
                'initial' => $initial,
                'foto' => $foto,
                'photographer_id' => $photographerId,
                'last_message' => $t->lastMessage ? $t->lastMessage->message : 'Belum ada pesan',
                'last_message_time' => $lastMessageTime,
                'last_message_iso' => $lastMessageIso,
                'last_timestamp' => $lastTimestamp,
                'unread_count' => $t->messages()->where('id_sender', '!=', $user->id)->where('is_read', false)->count(),
                'status_user' => $status_user,
            ];
        });
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

        if (!$activeThreadId && $formattedThreads->count() > 0) {
            $activeThreadId = $formattedThreads->first()['id'];
        }

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
                    'status_user' => self::formatLastSeen($photoUser ? $photoUser->last_seen_at : null),
                ];

                $messages = ChatMessage::where('id_chat_booking', $activeThreadModel->id)
                    ->with('sender')
                    ->orderBy('created_at', 'asc')
                    ->get();
            }
        }

        $formattedMessages = $messages->map(function ($msg) use ($user) {
            return [
                'id' => $msg->id,
                'id_sender' => $msg->id_sender,
                'sender_name' => $msg->sender->nama ?? 'Pengguna',
                'message' => $msg->message,
                'is_me' => (int) $msg->id_sender === (int) $user->id,
                'is_read' => $msg->is_read,
                'created_at' => $msg->created_at ? $msg->created_at->toIso8601String() : null,
                'time' => $msg->created_at ? $msg->created_at->format('H:i') : '',
            ];
        });

        return view('public.chat.index', compact('threads', 'formattedThreads', 'activeThread', 'formattedMessages'));
    }

    /**
     * Tampilkan Halaman Chat Studio Fotografer.
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
        if (!$activeThreadId && $formattedThreads->count() > 0) {
            $activeThreadId = $formattedThreads->first()['id'];
        }

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
                    'status_user' => self::formatLastSeen($clientUser ? $clientUser->last_seen_at : null),
                ];

                $messages = ChatMessage::where('id_chat_booking', $activeThreadModel->id)
                    ->with('sender')
                    ->orderBy('created_at', 'asc')
                    ->get();
            }
        }

        $formattedMessages = $messages->map(function ($msg) use ($user) {
            return [
                'id' => $msg->id,
                'id_sender' => $msg->id_sender,
                'sender_name' => $msg->sender->nama ?? 'Pengguna',
                'message' => $msg->message,
                'is_me' => (int) $msg->id_sender === (int) $user->id,
                'is_read' => $msg->is_read,
                'created_at' => $msg->created_at ? $msg->created_at->toIso8601String() : null,
                'time' => $msg->created_at ? $msg->created_at->format('H:i') : '',
            ];
        });

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

        if ($user->role === 'photographer') {
            $otherUser = $thread->client->user ?? null;
            $otherName = $thread->client->nama ?? 'Klien';
            $otherAvatar = self::resolveAvatarUrl($thread->client->foto ?? null);
            $otherInitial = strtoupper(substr($otherName, 0, 2));
        } else {
            $otherUser = $thread->photographer->user ?? null;
            $otherName = $thread->photographer->nama ?? 'Fotografer';
            $otherAvatar = self::resolveAvatarUrl($thread->photographer->foto ?? null);
            $otherInitial = strtoupper(substr($otherName, 0, 2));
            $photographerId = $thread->photographer->id ?? null;
        }

        $otherStatus = self::formatLastSeen($otherUser ? $otherUser->last_seen_at : null);

        $messages = ChatMessage::where('id_chat_booking', $thread->id)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($msg) use ($user) {
                return [
                    'id' => $msg->id,
                    'id_sender' => $msg->id_sender,
                    'sender_name' => $msg->sender->nama ?? 'Pengguna',
                    'message' => $msg->message,
                    'is_me' => (int) $msg->id_sender === (int) $user->id,
                    'is_read' => $msg->is_read,
                    'created_at' => $msg->created_at->toIso8601String(),
                    'time' => $msg->created_at->format('H:i'),
                ];
            });

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
            'other_status' => $otherStatus,
            'messages' => $messages,
            'threads' => $formattedThreads,
        ]);
    }

    /**
     * Kirim Pesan Baru ke Thread Chat.
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'id_chat_booking' => ['required', 'exists:chat_bookings,id'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $user = Auth::user();
        $user->update(['last_seen_at' => now()]);

        $thread = ChatBooking::findOrFail($request->id_chat_booking);

        $msg = ChatMessage::create([
            'id_chat_booking' => $thread->id,
            'id_sender' => $user->id,
            'message' => $request->message,
            'is_read' => false,
        ]);

        $thread->touch();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $msg->id,
                    'id_sender' => $msg->id_sender,
                    'sender_name' => $user->nama,
                    'message' => $msg->message,
                    'is_me' => true,
                    'is_read' => false,
                    'created_at' => $msg->created_at->toIso8601String(),
                    'time' => $msg->created_at->format('H:i'),
                ],
            ]);
        }

        return redirect()->back();
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
}