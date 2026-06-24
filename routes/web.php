<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/admin', function () {
    return view('admin.dashboard');
});

Route::get('/petugas', function () {
    return view('petugas.dashboard');
});
