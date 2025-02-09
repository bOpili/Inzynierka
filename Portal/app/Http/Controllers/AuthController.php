<?php

namespace App\Http\Controllers;

use App\Events\NotificationNumChange;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AuthController extends Controller
{

    public function create(){
        return Inertia::render('Auth/Login', [
            'status' => session('status')
        ]);
    }
    public function register(Request $request){

        $fields = $request->validate([
            'name' => ['required', 'max:32', 'unique:users'],
            'email' => ['required', 'max:128', 'email', 'unique:users'],
            'password' => ['required', 'confirmed'],
            'timezone' => ['required','timezone']
        ]);

        $fields['profilepic'] = 'ProfilePictures/defaultpfp.jpg';

        $user = User::create($fields);

        Auth::login($user);

        event(new Registered($user));

        return redirect()->route('home')->with('message', "Welcome " . $user->name . ", verifiaction mail has been send to Your address");
    }

    public function login(Request $request){
        $fields = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($fields, $request->remember)) {
            $request->session()->regenerate();

            return redirect()->intended('/')->with('message', 'Welcome back '.Auth::user()->name);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request){
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    public function notice(){
        return Inertia::render('Auth/VerifyEmail',['email' => Auth::user()->email, 'status' => session('status')]);
    }

    public function handler(EmailVerificationRequest $request){
        $request->fulfill();

        return redirect()->route(('home'));
    }

    public function resend(Request $request){
        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'Verification link sent!');
    }
}
