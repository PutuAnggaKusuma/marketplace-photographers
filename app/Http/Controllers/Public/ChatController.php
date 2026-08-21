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
     * Helper Formatter Status Online / Last Seen Shopee Style.
     */
    public static function formatLastSeen($lastSeenAt): array
    {
        if (!$lastSeenAt) {
            return [
                'is_online' => false,
                'label' => 'Aktif 7+ hari lalu',
                'color' => 'text-gray-400 dark:text-gray-500',
                'dot_color' => 'bg-gray-400',
            ];
        }

        if (is_string($lastSeenAt)) {
            $lastSeenAt = Carbon::parse($lastSeenAt);
        } elseif (!$lastSeenAt instanceof Carbon) {
            return [
                'is_online' => false,
                'label' => 'Aktif 7+ hari lalu',
                'color' => 'text-gray-400 dark:text-gray-500',
                'dot_color' => 'bg-gray-400',
            ];
        }

        $now = now();
        $diffMinutes = $lastSeenAt->diffInMinutes($now);

        if ($diffMinutes < 3) {
            return [
                'is_online' => true,
                'label' => 'Online',
                'color' => 'text-emerald-600 dark:text-emerald-400',
                'dot_color' => 'bg-emerald-500',
            ];
        }

        $diffHours = $lastSeenAt->diffInHours($now);
        $diffDays = $lastSeenAt->diffInDays($now);

        $intMinutes = (int) $diffMinutes;
        $intHours = (int) $diffHours;
        $intDays = (int) $diffDays;

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

        $threads = ChatBooking::where('id_client', $client->id)
            ->with(['photographer.user', 'lastMessage'])
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($t) {
                $photoUser = $t->photographer->user ?? null;
                $t->status_user = self::formatLastSeen($photoUser ? $photoUser->last_seen_at : null);
                if ($t->lastMessage && $t->lastMessage->created_at) {
                    $t->last_message_time = $t->lastMessage->created_at->setTimezone('Asia/Jakarta')->format('H:i');
                    $t->last_message_iso = $t->lastMessage->created_at->toIso8601String();
                } else {
                    $t->last_message_time = '';
                    $t->last_message_iso = '';
                }
                return $t;
            });

        $activeThreadId = $request->query('thread');
        if (!$activeThreadId && $threads->count() > 0) {
            $activeThreadId = $threads->first()->id;
        }

        $activeThread = null;
        $messages = collect();

        if ($activeThreadId) {
            $activeThread = ChatBooking::where('id_client', $client->id)
                ->where('id', $activeThreadId)
                ->with(['photographer.user'])
                ->first();

            if ($activeThread) {
                $photoUser = $activeThread->photographer->user ?? null;
                $activeThread->status_user = self::formatLastSeen($photoUser ? $photoUser->last_seen_at : null);

                // Mark unread messages from photographer as read
                ChatMessage::where('id_chat_booking', $activeThread->id)
                    ->where('id_sender', '!=', $user->id)
                    ->where('is_read', false)
                    ->update(['is_read' => true]);

                $messages = ChatMessage::where('id_chat_booking', $activeThread->id)
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

        return view('public.chat.index', compact('threads', 'activeThread', 'formattedMessages'));
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

        $threads = ChatBooking::where('id_photographer', $photographer->id)
            ->with(['client.user', 'lastMessage'])
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($t) {
                $clientUser = $t->client->user ?? null;
                $t->status_user = self::formatLastSeen($clientUser ? $clientUser->last_seen_at : null);
                if ($t->lastMessage && $t->lastMessage->created_at) {
                    $t->last_message_time = $t->lastMessage->created_at->setTimezone('Asia/Jakarta')->format('H:i');
                    $t->last_message_iso = $t->lastMessage->created_at->toIso8601String();
                } else {
                    $t->last_message_time = '';
                    $t->last_message_iso = '';
                }
                return $t;
            });

        $activeThreadId = $request->query('thread');
        if (!$activeThreadId && $threads->count() > 0) {
            $activeThreadId = $threads->first()->id;
        }

        $activeThread = null;
        $messages = collect();

        if ($activeThreadId) {
            $activeThread = ChatBooking::where('id_photographer', $photographer->id)
                ->where('id', $activeThreadId)
                ->with(['client.user'])
                ->first();

            if ($activeThread) {
                $clientUser = $activeThread->client->user ?? null;
                $activeThread->status_user = self::formatLastSeen($clientUser ? $clientUser->last_seen_at : null);

                // Mark unread messages from client as read
                ChatMessage::where('id_chat_booking', $activeThread->id)
                    ->where('id_sender', '!=', $user->id)
                    ->where('is_read', false)
                    ->update(['is_read' => true]);

                $messages = ChatMessage::where('id_chat_booking', $activeThread->id)
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

        return view('photographer.chat.index', compact('threads', 'activeThread', 'formattedMessages'));
    }

    /**
     * Memulai atau Membuka Thread Chat dengan Fotografer Spesifik.
     */
    public function startChat($photographerId)
    {
        $user = Auth::user();
        $user->update(['last_seen_at' => now()]);

        $client = RoleClient::firstOrCreate(['id_user' => $user->id], [
            'nama' => $user->nama,
            'nomor_telepon' => '081234567890',
            'alamat' => 'Jakarta, Indonesia',
        ]);

        $photographer = RolePhotographer::findOrFail($photographerId);

        $thread = ChatBooking::firstOrCreate([
            'id_photographer' => $photographer->id,
            'id_client' => $client->id,
        ]);

        if ($thread->messages()->count() === 0) {
            ChatMessage::create([
                'id_chat_booking' => $thread->id,
                'id_sender' => $photographer->id_user,
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
     * Ambil Pesan Terbaru via JSON (Dipakai oleh Alpine.js Polling).
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
        if ($user->role === 'photographer') {
            $otherUser = $thread->client->user ?? null;
        } else {
            $otherUser = $thread->photographer->user ?? null;
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

        return response()->json([
            'success' => true,
            'other_status' => $otherStatus,
            'messages' => $messages,
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
}