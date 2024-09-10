<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Presensi;

Route::group(['middleware' => 'auth'], function(){
    Route::get('presensi', Presensi::class)->name('presensi');
});

// login first before accessing the presensi page
Route::get('/login', function(){
    return redirect('admin/login');
})->name('login');

Route::get('/', function () {
    return view('welcome');
});
