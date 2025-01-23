<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Event;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ChatController extends Controller
{
    public function fetchMessages($eventId)
    {
        $event = Event::findOrFail($eventId);

        // Ensure the user is part of the event (add your own validation here)
        $canSeeMessage = Gate::inspect('seeMessages', $event);

        if($canSeeMessage->denied()){
            return redirect()->back()->with(['message' => $canSeeMessage->message()]);
        }

        // Fetch messages for the event
        return Message::where('event_id', $eventId)
            ->select('message','sender_id')
            ->with('sender:id,name,profilepic') // Include sender's name
            ->take(50) // Fetch the last 50 messages
            ->get();
    }

    public function sendMessage(Request $request, $eventId)
    {
        $request->validate( [
            'message' => 'required|string|max:1000',
        ]);

        $message = Message::create([
            'event_id' => $eventId,
            'sender_id' => Auth::id(),
            'message' => $request->message,
        ]);

        // Broadcast the message to other users in real-time
        broadcast(new MessageSent($message));

        return $message;
    }
}
