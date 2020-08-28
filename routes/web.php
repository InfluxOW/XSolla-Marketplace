<?php

use App\Mail\SendKeyToTheBuyer;
use App\Providers\RouteServiceProvider;
use App\Purchase;
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
    return new SendKeyToTheBuyer(Purchase::find(1));
});
