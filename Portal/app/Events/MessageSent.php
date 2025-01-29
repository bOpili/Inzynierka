<?php

namespace App\Events;

use App\Models\Message;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $message;
    protected $sender;
    protected $messageContent;


    public function __construct(Message $message)
    {
        $this->message = $message;
        $this->messageContent = $message->message;
        $this->sender = User::where('id',$message->sender_id)->select(['id','name','profilepic'])->first();
    }

    public function broadcastWith(): array{
        return[
            'message' => $this->messageContent,
            'sender' => $this->sender
        ];
    }

    /**
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PresenceChannel('event-chat.' . $this->message->event_id),
        ];
    }
}
