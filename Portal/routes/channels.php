<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('event-chat.{eventId}', function ($user, $eventId){
    return $user->events->contains($eventId);
});

