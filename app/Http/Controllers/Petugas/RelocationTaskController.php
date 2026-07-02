<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\RelocationTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RelocationTaskController extends Controller
{
    public function index()
    {
        $tasks = RelocationTask::with('item', 'fromLocation', 'toLocation')
            ->where('status', 'pending')
            ->latest()
            ->paginate(15);

        $completedToday = RelocationTask::where('status', 'completed')
            ->whereDate('completed_at', today())
            ->count();

        return view('petugas.relocation_tasks.index', compact('tasks', 'completedToday'));
    }

    public function complete(RelocationTask $task)
    {
        if ($task->status !== 'pending') {
            return redirect()->back()->with('error', 'Tugas ini sudah selesai atau dibatalkan.');
        }

        $messages = [];

        DB::transaction(function () use ($task, &$messages) {
            $item      = $task->item;
            $fromLocId = $task->from_location_id;
            $toLocId   = $task->to_location_id;
            $qty       = $task->quantity;

            // --- 1. Kurangi pivot quantity di lokasi ASAL ---
            $fromPivot = DB::table('item_location')
                ->where('item_id', $item->id)
                ->where('location_id', $fromLocId)
                ->first();

            if ($fromPivot) {
                $newFromQty = max(0, $fromPivot->quantity - $qty);
                if ($newFromQty <= 0) {
                    DB::table('item_location')
                        ->where('item_id', $item->id)
                        ->where('location_id', $fromLocId)
                        ->delete();
                } else {
                    DB::table('item_location')
                        ->where('item_id', $item->id)
                        ->where('location_id', $fromLocId)
                        ->update(['quantity' => $newFromQty, 'updated_at' => now()]);
                }
            }
            Location::syncFill($fromLocId);

            // --- 2. Cek kapasitas lokasi TUJUAN (BEST PRACTICE: selalu cek sebelum commit) ---
            $toLoc      = Location::find($toLocId);
            $freshFill  = DB::table('item_location')->where('location_id', $toLocId)->sum('quantity');
            $spaceLeft  = $toLoc->capacity - $freshFill;

            // Batasi: khusus BLK, anggap selalu ada ruang
            $isBulk = $toLoc->zone === 'BLK' || $toLoc->storage_class === 'general';
            if ($isBulk) {
                $spaceLeft = $qty; // Bulk selalu muat
            }

            $qtyFitsTarget = min($qty, max(0, $spaceLeft));
            $qtyOverflow   = $qty - $qtyFitsTarget;

            // Masukkan yang muat ke lokasi tujuan
            if ($qtyFitsTarget > 0) {
                $toPivot = DB::table('item_location')
                    ->where('item_id', $item->id)
                    ->where('location_id', $toLocId)
                    ->first();

                if ($toPivot) {
                    DB::table('item_location')
                        ->where('item_id', $item->id)
                        ->where('location_id', $toLocId)
                        ->update(['quantity' => $toPivot->quantity + $qtyFitsTarget, 'updated_at' => now()]);
                } else {
                    DB::table('item_location')->insert([
                        'item_id'     => $item->id,
                        'location_id' => $toLocId,
                        'quantity'    => $qtyFitsTarget,
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ]);
                }
                Location::syncFill($toLocId);
            }

            // --- 3. Overflow → Redirect otomatis ke BLK ---
            if ($qtyOverflow > 0) {
                // Cari BLK dengan sisa terbesar (paling longgar)
                $bulkLoc = Location::where(function($q) {
                        $q->where('storage_class', 'general')->orWhere('zone', 'BLK');
                    })
                    ->orderByRaw('(capacity - current_fill) DESC')
                    ->first();

                if ($bulkLoc) {
                    $bulkPivot = DB::table('item_location')
                        ->where('item_id', $item->id)
                        ->where('location_id', $bulkLoc->id)
                        ->first();

                    if ($bulkPivot) {
                        DB::table('item_location')
                            ->where('item_id', $item->id)
                            ->where('location_id', $bulkLoc->id)
                            ->update(['quantity' => $bulkPivot->quantity + $qtyOverflow, 'updated_at' => now()]);
                    } else {
                        DB::table('item_location')->insert([
                            'item_id'     => $item->id,
                            'location_id' => $bulkLoc->id,
                            'quantity'    => $qtyOverflow,
                            'created_at'  => now(),
                            'updated_at'  => now(),
                        ]);
                    }
                    Location::syncFill($bulkLoc->id);

                    $messages[] = "⚠️ {$qtyOverflow} pcs sisanya tidak muat di {$toLoc->code}, otomatis disimpan di zona bulk {$bulkLoc->code}.";
                }
            }

            // --- 4. Update status tugas menjadi completed ---
            $task->update([
                'status'       => 'completed',
                'completed_by' => auth()->id(),
                'completed_at' => now(),
            ]);
        });

        $successMsg = "✅ Barang berhasil dipindahkan! Lokasi stok telah diperbarui.";
        if (!empty($messages)) {
            $successMsg .= " " . implode(' ', $messages);
        }

        return redirect()->route('petugas.relocationTasks.index')
            ->with('success', $successMsg);
    }
}
