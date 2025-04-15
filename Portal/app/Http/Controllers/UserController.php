<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class UserController extends Controller
{
    public function showDashboard() {
        return Inertia::render('Auth/Dashboard',['user' => User::findOrFail(Auth::id())]);
    }


    public function editProfile(Request $request){
        $request->validate([
            'pfp' => ['file', 'nullable'],
            'timezone' => ['timezone']
        ]);

        if ($request->hasFile('pfp')) {
            $user = User::findOrFail(Auth::id());
            if($user->profilepic != "ProfilePictures/defaultpfp.jpg")
            Storage::disk('public')->delete($user->profilepic);
            $pfp = Storage::disk('public')->put('ProfilePictures',$request->pfp);
            $user->update(['profilepic' => $pfp]);
        }

        if ($request->timezone){
            User::findOrFail(Auth::id())->update(['timezone' => $request->timezone]);
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
