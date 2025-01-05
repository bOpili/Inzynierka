<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class UserController extends Controller
{
    public function changePfp(Request $request){
        $request->validate([
            'pfp' => ['file', 'nullable']
        ]);

        if ($request->hasFile('pfp')) {
            $pfp = Storage::disk('public')->put('ProfilePictures',$request->pfp);
            User::find(Auth::id())->update(['profilepic' => $pfp]);
        }
    }

    public function showUsers(){
        $requests = User::findOrFail(Auth::id())->receivedFriendRequests()->where('status', 'pending')->with('sender:id,name,profilepic')->get();
        $invites = User::findOrFail(Auth::id())->receivedInvitations()->where('status','pending')->with('sender:id,name,profilepic')->with('event:id,title')->get();

        if($requests->isEmpty()) {
            $requests = null;
        }
        if($invites->isEmpty()) {
            $invites = null;
        }

        $users = User::findOrFail(Auth::id())->friends;
        $friends = $users;
        return Inertia::render('Auth/Users',['users'=>$users, 'requests' => $requests, 'friends' => $friends, 'invites' => $invites]);
    }

    public function findUser(Request $request){
        $found = User::where('name','=',$request->Name)->get();
        $friends = User::findOrFail(Auth::id())->friends;

        if($found->isEmpty()){
            return back();
        }

        return Inertia::render('Auth/Users',['users'=>$found, 'friends' => $friends]);
    }

}
