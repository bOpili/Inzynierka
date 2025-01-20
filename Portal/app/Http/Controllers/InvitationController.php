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

        $user = User::findOrFail(Auth::id()); // Get the authenticated user
        $event = Event::findOrFail($request->eventId);

        // Ensure the user is the event creator
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

        // Prepare invitations using collections
        $invitation =
            [
                'event_id' => $event->id,
                'sender_id' => $user->id,
                'receiver_id' => $request->friendId,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ];

        // Insert invitations into the database
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
