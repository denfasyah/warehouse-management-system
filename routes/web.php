<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    // Shared Routes
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread-count', [\App\Http\Controllers\NotificationController::class, 'unreadCount'])->name('notifications.unreadCount');
    Route::post('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('notifications.readAll');
});

// ADMIN ROUTES
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        $stats = \App\Services\CBSService::getDashboardStats();
        return view('admin.dashboard', compact('stats'));
    })->name('dashboard');

    // Master Data
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class)->except(['create', 'show', 'edit']);
    Route::resource('locations', \App\Http\Controllers\Admin\LocationController::class)->except(['create', 'show', 'edit']);
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->except(['create', 'show', 'edit']);
    
    // Persetujuan Barang Keluar
    Route::get('approvals', [\App\Http\Controllers\Admin\ApprovalController::class, 'index'])->name('approvals.index');
    Route::post('approvals/{outgoing}/approve', [\App\Http\Controllers\Admin\ApprovalController::class, 'approve'])->name('approvals.approve');
    Route::post('approvals/{outgoing}/reject', [\App\Http\Controllers\Admin\ApprovalController::class, 'reject'])->name('approvals.reject');
    
    // Items & Barcode
    Route::get('items/export', [\App\Http\Controllers\Admin\ItemController::class, 'exportCsv'])->name('items.export');
    Route::resource('items', \App\Http\Controllers\Admin\ItemController::class)->except(['show']);
    Route::get('items/{item}/barcode', [\App\Http\Controllers\Admin\BarcodeController::class, 'print'])->name('barcode.print');

    // Class-Based Storage (CBS)
    Route::get('settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
    
    Route::get('cbs/classification', [\App\Http\Controllers\Admin\CBSController::class, 'classification'])->name('cbs.classification');
    Route::post('cbs/recalculate', [\App\Http\Controllers\Admin\CBSController::class, 'recalculate'])->name('cbs.recalculate');
    Route::post('cbs/generate-relocation', [\App\Http\Controllers\Admin\CBSController::class, 'generateRelocationTasks'])->name('cbs.generateRelocation');
    Route::get('cbs/mapping', [\App\Http\Controllers\Admin\CBSController::class, 'mapping'])->name('cbs.mapping');
    Route::get('cbs/relocation-tasks', [\App\Http\Controllers\Admin\CBSController::class, 'relocationTasks'])->name('cbs.relocationTasks');
    Route::post('cbs/relocation-tasks/{task}/cancel', [\App\Http\Controllers\Admin\CBSController::class, 'cancelTask'])->name('cbs.cancelTask');
});

// PETUGAS ROUTES
Route::middleware(['auth', 'role:petugas'])->prefix('petugas')->name('petugas.')->group(function () {
    Route::get('/', function () {
        $stats = \App\Services\CBSService::getDashboardStats();
        
        // Ambil riwayat aktivitas terbaru dari Petugas ini
        $recentIncomings = \App\Models\IncomingGood::with('item', 'location')
            ->where('user_id', auth()->id())
            ->latest()
            ->limit(5)
            ->get()
            ->map(function($inc) {
                return [
                    'time' => $inc->created_at->format('H:i'),
                    'act' => 'Barang Masuk',
                    'item' => $inc->item->name,
                    'loc' => $inc->location ? $inc->location->code : '-',
                    'status' => 'Berhasil',
                    'ok' => true,
                    'created_at' => $inc->created_at
                ];
            });

        $recentOutgoings = \App\Models\OutgoingGood::with('item', 'location')
            ->where('requested_by', auth()->id())
            ->latest()
            ->limit(5)
            ->get()
            ->map(function($out) {
                $statusLabel = $out->status == 'approved' ? 'Berhasil' : ($out->status == 'rejected' ? 'Ditolak' : 'Pending');
                $ok = $out->status == 'approved';
                return [
                    'time' => $out->created_at->format('H:i'),
                    'act' => 'Barang Keluar',
                    'item' => $out->item->name,
                    'loc' => $out->location ? $out->location->code : '-',
                    'status' => $statusLabel,
                    'ok' => $ok,
                    'created_at' => $out->created_at
                ];
            });

        $activities = $recentIncomings->concat($recentOutgoings)
            ->sortByDesc('created_at')
            ->take(5);

        return view('petugas.dashboard', compact('stats', 'activities'));
    })->name('dashboard');

    // Inbound (Barang Masuk)
    Route::get('incoming/search', [\App\Http\Controllers\Petugas\IncomingGoodController::class, 'search'])->name('incoming.search');
    Route::resource('incoming', \App\Http\Controllers\Petugas\IncomingGoodController::class)->only(['index', 'create', 'store']);

    // Scanner
    Route::get('scanner', [\App\Http\Controllers\Petugas\ScannerController::class, 'index'])->name('scanner.index');
    Route::get('scanner/api', [\App\Http\Controllers\Petugas\ScannerController::class, 'scan'])->name('scanner.api');

    // Outbound (Barang Keluar)
    Route::resource('outgoing', \App\Http\Controllers\Petugas\OutgoingGoodController::class)->only(['index', 'create', 'store']);

    // CBS untuk Petugas (Storage)
    Route::get('cbs/locations', [\App\Http\Controllers\Petugas\CBSController::class, 'locations'])->name('cbs.locations');
    Route::get('cbs/arrangement', [\App\Http\Controllers\Petugas\CBSController::class, 'arrangement'])->name('cbs.arrangement');

    // Tugas Relokasi untuk Petugas
    Route::get('relocation-tasks', [\App\Http\Controllers\Petugas\RelocationTaskController::class, 'index'])->name('relocationTasks.index');
    Route::post('relocation-tasks/{task}/complete', [\App\Http\Controllers\Petugas\RelocationTaskController::class, 'complete'])->name('relocationTasks.complete');
});
