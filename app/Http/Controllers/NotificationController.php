<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Tampilkan halaman notifikasi dengan filter dan pagination.
     * Tidak auto-mark-all-read — biarkan user yang memilih.
     */
    public function index(Request $request)
    {
        $query = auth()->user()->notifications();

        // Filter: status baca
        $filter = $request->input('filter', 'all'); // all | unread | read
        if ($filter === 'unread') {
            $query->whereNull('read_at');
        } elseif ($filter === 'read') {
            $query->whereNotNull('read_at');
        }

        // Filter: tipe notifikasi
        $type = $request->input('type'); // approved | rejected | info
        if ($type) {
            $query->where('type', $type);
        }

        $notifications = $query->paginate(15)->withQueryString();

        return view('notifications.index', compact('notifications', 'filter'));
    }

    /**
     * Endpoint API polling — mengambil jumlah unread.
     */
    public function unreadCount()
    {
        $count = auth()->user()->unreadNotifications()->count();
        return response()->json(['count' => $count]);
    }

    /**
     * Tandai semua notifikasi sebagai telah dibaca.
     */
    public function markAllRead()
    {
        auth()->user()->unreadNotifications()->update(['read_at' => now()]);
        return back()->with('success', 'Semua notifikasi telah ditandai sebagai dibaca.');
    }
}
