<?php

use App\Mail\SendKeyToThePayer;
use App\Providers\RouteServiceProvider;
use App\Payment;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect(RouteServiceProvider::HOME);
});

Route::get('mailable', function () {
    return new SendKeyToThePayer(Payment::find(1));
});
