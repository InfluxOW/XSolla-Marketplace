<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/* Auth */
Route::post('login', 'API\Auth\LoginController@login')->name('login');
Route::post('register', 'API\Auth\RegisterController@register')->name('register');
/* Games */
Route::apiResource('games', 'API\GamesController')->only('index', 'store', 'show')->parameters(['games' => 'game:slug']);
