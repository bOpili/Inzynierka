<?php

namespace App\Http\Controllers;

use App\Events\NotificationNumChange;
use App\Models\FriendRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FriendController extends Controller
{
    public function sendRequest(Request $request, $receiverId)
    {
        $sender = User::findOrFail(Auth::id());

        if (
            FriendRequest::where('sender_id', $sender->id)->where('receiver_id', $receiverId)->exists() ||
            $sender->friends()->where('friend_id', $receiverId)->exists()
        ) {
            return back()->with('message','Friend request already sent or already friends');
        }

        FriendRequest::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiverId,
            'status' => 'pending',
        ]);

        broadcast(new NotificationNumChange(User::firstWhere('id','=',$receiverId)));

        return back()->with('message', 'Friend request sent successfully');
    }

    public function acceptRequest(Request $request, $requestId)
    {
        $friendRequest = FriendRequest::findOrFail($requestId);

        if ($friendRequest->receiver_id != Auth::id()) {
            return back()->with('message', 'Unauthorized');
        }

        User::findOrFail(Auth::id())->friends()->attach($friendRequest->sender_id);
        $friendRequest->sender->friends()->attach(Auth::id());

        $friendRequest->delete();

        broadcast(new NotificationNumChange(User::findOrFail(Auth::id())));

        return back()->with('message', 'Friend request accepted');
    }

    public function rejectRequest(Request $request, $requestId)
    {
        $friendRequest = FriendRequest::findOrFail($requestId);

        if ($friendRequest->receiver_id != Auth::id()) {
            return back()->with('message','Unauthorized');
        }

        $friendRequest->delete();

        broadcast(new NotificationNumChange(User::findOrFail(Auth::id())));

        return back()->with('message','Friend request rejected');
    }


    public function removeFriend(Request $request, $friendId){
        User::find(Auth::id())->friends()->detach($friendId);
        User::find($friendId)->friends()->detach(Auth::id());

        return back()->with('message','User removed from fiend list');
    }

}


