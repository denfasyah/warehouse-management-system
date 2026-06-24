<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ADMIN ROUTES
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // Master Data
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class)->except(['create', 'show', 'edit']);
    Route::resource('locations', \App\Http\Controllers\Admin\LocationController::class)->except(['create', 'show', 'edit']);
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->except(['create', 'show', 'edit']);
    
    // Items & Barcode
    Route::get('items/export', [\App\Http\Controllers\Admin\ItemController::class, 'exportCsv'])->name('items.export');
    Route::resource('items', \App\Http\Controllers\Admin\ItemController::class)->except(['show']);
    Route::get('items/{item}/barcode', [\App\Http\Controllers\Admin\BarcodeController::class, 'print'])->name('barcode.print');
});

// PETUGAS ROUTES
Route::middleware(['auth', 'role:petugas'])->prefix('petugas')->name('petugas.')->group(function () {
    Route::get('/', function () {
        return view('petugas.dashboard');
    })->name('dashboard');

    // Inbound (Barang Masuk)
    Route::get('incoming/search', [\App\Http\Controllers\Petugas\IncomingGoodController::class, 'search'])->name('incoming.search');
    Route::resource('incoming', \App\Http\Controllers\Petugas\IncomingGoodController::class)->only(['index', 'create', 'store']);

    // Scanner
    Route::get('scanner', [\App\Http\Controllers\Petugas\ScannerController::class, 'index'])->name('scanner.index');
    Route::get('scanner/api', [\App\Http\Controllers\Petugas\ScannerController::class, 'scan'])->name('scanner.api');

    // Outbound (Barang Keluar)
    Route::resource('outgoing', \App\Http\Controllers\Petugas\OutgoingGoodController::class)->only(['index', 'create', 'store']);
});
