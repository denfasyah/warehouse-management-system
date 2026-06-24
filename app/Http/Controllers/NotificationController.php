<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Tampilkan semua notifikasi untuk user yang sedang login.
     */
    public function index()
    {
        $notifications = auth()->user()->notifications()->paginate(20);

        // Otomatis tandai semua sebagai dibaca ketika halaman ini diakses
        auth()->user()->unreadNotifications()->update(['read_at' => now()]);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Endpoint untuk API polling (mengambil jumlah unread)
     */
    public function unreadCount()
    {
        $count = auth()->user()->unreadNotifications()->count();
        return response()->json(['count' => $count]);
    }

    /**
     * Endpoint untuk menandai semua notifikasi sebagai telah dibaca
     */
    public function markAllRead()
    {
        auth()->user()->unreadNotifications()->update(['read_at' => now()]);
        return back()->with('success', 'Semua notifikasi telah ditandai sebagai dibaca.');
    }
}
