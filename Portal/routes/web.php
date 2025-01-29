<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
require __DIR__ . '/user/auth.php';
require __DIR__ . '/user/guest.php';

//---Home page---//
Route::get('/', [HomeController::class, 'index'])->name('home');

