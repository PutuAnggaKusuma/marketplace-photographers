<?php

namespace App\Events;

use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $chatBookingId;

    /**
     * Create a new event instance.
     */
    public function __construct(ChatMessage $message, ?User $sender = null)
    {
        $this->chatBookingId = $message->id_chat_booking;
        
        $this->message = [
            'id' => $message->id,
            'id_sender' => $message->id_sender,
            'sender_name' => $message->sender->nama ?? ($sender->nama ?? 'Pengguna'),
            'message' => $message->deleted_for_all ? 'Pesan ini telah dihapus' : $message->message,
            'reply_to' => $message->reply_to_id ? [
                'id' => $message->replyTo->id ?? $message->reply_to_id,
                'sender_name' => $message->replyTo->sender->nama ?? 'Pengguna',
                'message' => $message->replyTo ? ($message->replyTo->deleted_for_all ? 'Pesan ini telah dihapus' : $message->replyTo->message) : null,
            ] : null,
            'attachment_url' => $message->deleted_for_all ? null : $message->attachment_url,
            'attachment_type' => $message->deleted_for_all ? null : ($message->attachment_type ?? 'image'),
            'is_read' => (bool) $message->is_read,
            'is_edited' => (bool) $message->is_edited,
            'deleted_for_all' => (bool) $message->deleted_for_all,
            'created_at' => $message->created_at ? $message->created_at->toIso8601String() : now()->toIso8601String(),
            'time' => $message->created_at ? $message->created_at->format('H:i') : date('H:i'),
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat.' . $this->chatBookingId),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'MessageSent';
    }
}

