<?php

namespace App\Http\Controllers;

use App\Events\NotificationNumChange;
use App\Models\Event;
use App\Models\invitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class InvitationController extends Controller
{

    public function sendInvitations(Request $request)
    {
        $user = User::findOrFail(Auth::id());
        $event = Event::findOrFail($request->eventId);

        $auth = Gate::inspect('invite', $event);
        

        if (!($auth->allowed())) {
            return back()->with(['message' => $auth->message()]);
        }

        if (!Invitation::where('receiver_id', '=', $request->friendId)->where('event_id', '=', $event->id)->where('status', '=', 'pending')->get()->isEmpty()) {
            return back()->withErrors(['msg' => 'User already invited']);
        }
        

        if(!$event->users()->where('user_id',$request->friendId)->get()->isEmpty()){
            return back()->withErrors(['msg' => 'User already participates']);
        }

        $invitation =
            [
                'event_id' => $event->id,
                'sender_id' => $user->id,
                'receiver_id' => $request->friendId,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ];

        Invitation::insert($invitation);
    
        broadcast(new NotificationNumChange(User::findOrFail($request->friendId)));
        return back();
    }

    public function acceptInvitation(Request $request)
    {
        $invitation = invitation::findOrFail($request->id);
        $event = Event::findOrFail($invitation->event_id);
        $user = User::findOrFail($invitation->receiver_id);

        if ($event->users()->withPivot('status')->where('status', '>', 0)->count() < $event->slots) {
            $event->users()->attach($user);
            $event->users()->where('user_id', $user->id)->update(['status' => 1]);
            invitation::findOrFail($invitation->id)->delete();
        } else {
            $event->users()->where('user_id', $user->id)->update(['status' => -1]);
            invitation::findOrFail($invitation->id)->delete();
            return back()->with('message', 'No empty slots');
        }

        broadcast(new NotificationNumChange(User::findOrFail(Auth::id())));

        return back();
    }

    public function rejectInvitation(Request $request)
    {
        invitation::findOrFail($request->id)->delete();

        broadcast(new NotificationNumChange(User::findOrFail(Auth::id())));

        return back();
    }
}
