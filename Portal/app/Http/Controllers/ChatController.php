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

        $canSeeMessage = Gate::inspect('seeMessages', $event);

        if($canSeeMessage->denied()){
            return redirect()->back()->with(['message' => $canSeeMessage->message()]);
        }

        return Message::where('event_id', $eventId)
            ->select('message','sender_id')
            ->with('sender:id,name,profilepic')
            ->take(50)
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

        broadcast(new MessageSent($message));

        return $message;
    }
}
