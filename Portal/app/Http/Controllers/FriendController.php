<?php

namespace App\Http\Controllers;

use App\Events\NotificationNumChange;
use App\Models\FriendRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FriendController extends Controller
{
    // Send a friend request
    public function sendRequest(Request $request, $receiverId)
    {

        $sender = User::findOrFail(Auth::id());
        //$receiver = User::findOrFail($receiverId);

        // Check if request already exists or they are already friends
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

        broadcast(new NotificationNumChange(User::findOrFail($receiverId)));

        return back()->with('message', 'Friend request sent successfully');
    }

    // Accept a friend request
    public function acceptRequest(Request $request, $requestId)
    {
        $friendRequest = FriendRequest::findOrFail($requestId);

        if ($friendRequest->receiver_id != Auth::id()) {
            return back()->with('message', 'Unauthorized');
        }

        // $friendRequest->update(['status' => 'accepted']);

        // Create friendship in the pivot table
        User::findOrFail(Auth::id())->friends()->attach($friendRequest->sender_id);
        $friendRequest->sender->friends()->attach(Auth::id());

        $friendRequest->delete();

        broadcast(new NotificationNumChange(User::findOrFail(Auth::id())));

        return back()->with('message', 'Friend request accepted');
    }

    // Reject a friend request
    public function rejectRequest(Request $request, $requestId)
    {
        $friendRequest = FriendRequest::findOrFail($requestId);

        if ($friendRequest->receiver_id != Auth::id()) {
            return back()->with('message','Unauthorized');
        }

        // $friendRequest->update(['status' => 'rejected']);
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


