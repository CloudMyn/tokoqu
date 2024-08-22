<?php

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

    if (auth()->guest()) return redirect()->route('filament.store.auth.login');

    if (cek_admin_role())  return redirect()->route('filament.admin.pages.dashboard');

    return redirect()->route('filament.store.pages.dashboard');
});
