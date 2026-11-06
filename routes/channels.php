<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.{chatBookingId}', function ($user, $chatBookingId) {
    if (in_array($user->role, ['super_admin', 'admin'])) {
        return true;
    }

    $chat = \App\Models\ChatBooking::with(['client', 'photographer'])->find($chatBookingId);
    if (!$chat) {
        return false;
    }

    $isClient = $chat->client && (int) $chat->client->id_user === (int) $user->id;
    $isPhotographer = $chat->photographer && (int) $chat->photographer->id_user === (int) $user->id;

    return $isClient || $isPhotographer;
});
