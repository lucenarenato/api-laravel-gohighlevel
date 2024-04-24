<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('public/leadconnector', [App\Http\Controllers\PublicController::class, 'redirectToGHL'])->name('redirect.ghl');
Route::get('public/leadconnector/callback', [App\Http\Controllers\PublicController::class, 'storeGHL'])->name('ghl.callback');
Route::post('public/create-vendor-webhook', [App\Http\Controllers\PublicController::class, 'createVendorWebhook'])->name('create.vendor.webhook');
