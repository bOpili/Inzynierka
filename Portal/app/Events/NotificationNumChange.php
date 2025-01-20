<?php

namespace App\Events;

use App\Models\FriendRequest;
use App\Models\invitation;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationNumChange implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $non;

    /**
     * Create a new event instance.
     */
    public function __construct(protected User $user)
    {
        $this->non = $user->id ? invitation::where('status', 'pending')->where('receiver_id', $user->id)->count() + FriendRequest::where('status', 'pending')->where('receiver_id', $user->id)->count() : 0;
    }

    public function broadcastWith(): array{
        return [
            'non' => $this->non,
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.'.$this->user->id),
        ];
    }
}
