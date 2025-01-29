<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\UserController;
use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\Route;


Route::middleware('auth')->group(function () {
    //---Logout---//
    Route::post('/logout',[AuthController::class, 'logout'])->name('logout');

    //---Dashboard---//
    Route::post('/dashboard',[UserController::class, 'editProfile'])->middleware('verified')->name('editProfile');
    Route::get('/dashboard', [UserController::class, 'showDashboard'])->middleware('verified')->name('dashboard');

    //---Email verification---//
    Route::get('/email/verify', [AuthController::class, 'notice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'handler'])->middleware('signed')->name('verification.verify');
    Route::post('/email/verification-notification', [AuthController::class, 'resend'])->middleware('throttle:6,1')->name('verification.send');

    //---Users---//
    Route::get('users', [UserController::class, 'showUsers'])->middleware('verified')->name('users');
    Route::post('users', [UserController::class, 'findUser'])->name('users.findUser');

    //---Friends---//
    Route::post('/friend-requests/send/{id}', [FriendController::class, 'sendRequest'])->name('friend.sendRequest');
    Route::post('/friend-requests/accept/{id}', [FriendController::class, 'acceptRequest'])->name('friend.accept');
    Route::post('/friend-requests/reject/{id}', [FriendController::class, 'rejectRequest'])->name('friend.reject');
    Route::post('/friend/remove/{id}', [FriendController::class, 'removeFriend'])->name('friend.remove');

    //---Events---//
    Route::get('events', [EventController::class, 'index'])->middleware('verified')->name('events');
    Route::post('events/join', [EventController::class, 'join'])->middleware('verified')->name('event.join');
    Route::post('events/accept', [EventController::class, 'accept'])->middleware('verified')->name('event.accept');
    Route::post('events/leave', [EventController::class, 'leave'])->middleware('verified')->name('event.leave');
    Route::post('events/kick', [EventController::class, 'kick'])->middleware('verified')->name('event.kick');
    Route::post('event/invite',[InvitationController::class, 'sendInvitations'])->middleware('verified')->name('event.invite');
    Route::post('event/invite/accept',[InvitationController::class, 'acceptInvitation'])->middleware('verified')->name('event.invite.accept');
    Route::post('event/invite/reject',[InvitationController::class, 'rejectInvitation'])->middleware('verified')->name('event.invite.reject');
    Route::get('event/{event}/messages',[ChatController::class, 'fetchMessages'])->middleware('verified')->name('event.messages.fetch');
    Route::post('event/{event}/messages',[ChatController::class, 'sendMessage'])->middleware('verified')->name('event.messages.send');
    Route::resource('event',EventController::class)->except('index');

});



