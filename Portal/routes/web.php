<?php

use App\Events\NotificationNumChange;
use App\Http\Controllers\HomeController;
use App\Models\Event;
use App\Models\FriendRequest;
use App\Models\invitation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
require __DIR__ . '/user/auth.php';
require __DIR__ . '/user/guest.php';

//---Home page---//
Route::get('/', [HomeController::class, 'index'])->name('home');

// Route::get('/getNumberOfNotifications', function(){
//     return Response::json(['numberOfNotifications' => Auth::user() ? invitation::where('status', 'pending')->where('receiver_id', Auth::id())->count() + FriendRequest::where('status', 'pending')->where('receiver_id', Auth::id())->count() : 0]);
// });

// Route::get('/broadcast', function() {
//     broadcast(new NotificationNumChange(Auth::user()));
// });

