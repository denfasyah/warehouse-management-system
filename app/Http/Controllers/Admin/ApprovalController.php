<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OutgoingGood;
use App\Notifications\OutgoingGoodStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApprovalController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');
        
        $approvals = OutgoingGood::with(['item.category', 'requestedBy', 'location'])
            ->when($status !== 'all', function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->latest('requested_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.approvals.index', compact('approvals', 'status'));
    }

    public function approve(OutgoingGood $outgoing)
    {
        if ($outgoing->status !== 'pending') {
            return back()->with('error', 'Hanya pengajuan dengan status pending yang dapat disetujui.');
        }

        try {
            DB::beginTransaction();

            // Lock the item row to prevent race conditions
            $item = $outgoing->item()->lockForUpdate()->first();
            $location = $outgoing->location()->lockForUpdate()->first();

            if ($item->stock < $outgoing->quantity) {
                DB::rollBack();
                return back()->with('error', "Stok barang '{$item->name}' tidak mencukupi. Sisa stok: {$item->stock}, diminta: {$outgoing->quantity}.");
            }

            // Kurangi stok barang
            $item->decrement('stock', $outgoing->quantity);

            // Kurangi quantity di pivot item_location
            if ($location) {
                $pivotQty = \Illuminate\Support\Facades\DB::table('item_location')
                    ->where('item_id', $item->id)
                    ->where('location_id', $location->id)
                    ->value('quantity') ?? 0;
                
                $newPivotQty = max(0, $pivotQty - $outgoing->quantity);
                \Illuminate\Support\Facades\DB::table('item_location')
                    ->where('item_id', $item->id)
                    ->where('location_id', $location->id)
                    ->update(['quantity' => $newPivotQty]);

                // Sinkronkan current_fill dari pivot (satu titik terpusat)
                \App\Models\Location::syncFill($location->id);
            }

            // Update status outgoing
            $outgoing->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'processed_at' => now(),
            ]);

            // Kirim notifikasi ke petugas pengaju
            if ($outgoing->requestedBy) {
                \App\Models\Notification::send(
                    $outgoing->requested_by,
                    'approved',
                    'Permintaan Disetujui',
                    "Permintaan {$outgoing->item->name} Anda telah disetujui.",
                    [
                        'outgoing_good_id' => $outgoing->id,
                    ]
                );
            }

            DB::commit();

            // CBS recalculation tidak dijalankan otomatis.
            // Admin harus menekan tombol 'Jalankan Kalkulasi Sekarang' secara manual
            // agar alur demo/skripsi sesuai: input data → approve → kalkulasi manual → lihat hasil.

            return back()->with('success', "Pengajuan barang keluar '{$item->name}' berhasil disetujui.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Approval error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan sistem saat memproses persetujuan.');
        }
    }

    public function reject(Request $request, OutgoingGood $outgoing)
    {
        if ($outgoing->status !== 'pending') {
            return back()->with('error', 'Hanya pengajuan dengan status pending yang dapat ditolak.');
        }

        $request->validate([
            'reject_reason' => 'required|string|max:500'
        ], [
            'reject_reason.required' => 'Alasan penolakan wajib diisi agar petugas mengetahuinya.'
        ]);

        try {
            DB::beginTransaction();

            $outgoing->update([
                'status' => 'rejected',
                'approved_by' => auth()->id(),
                'reject_reason' => $request->reject_reason,
                'processed_at' => now(),
            ]);

            // Kirim notifikasi ke petugas pengaju
            if ($outgoing->requestedBy) {
                \App\Models\Notification::send(
                    $outgoing->requested_by,
                    'rejected',
                    'Permintaan Ditolak',
                    "Permintaan {$outgoing->item->name} Anda ditolak.",
                    [
                        'outgoing_good_id' => $outgoing->id,
                        'reject_reason' => $request->reject_reason,
                    ]
                );
            }

            DB::commit();

            return back()->with('success', "Pengajuan barang keluar berhasil ditolak.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Rejection error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan sistem saat memproses penolakan.');
        }
    }
}
