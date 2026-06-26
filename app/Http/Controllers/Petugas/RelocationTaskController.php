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

        DB::transaction(function () use ($task) {
            $item        = $task->item;
            $fromLocId   = $task->from_location_id;
            $toLocId     = $task->to_location_id;
            $qty         = $task->quantity;

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

            // --- 2. Tambah/buat pivot quantity di lokasi TUJUAN ---
            $toPivot = DB::table('item_location')
                ->where('item_id', $item->id)
                ->where('location_id', $toLocId)
                ->first();

            if ($toPivot) {
                DB::table('item_location')
                    ->where('item_id', $item->id)
                    ->where('location_id', $toLocId)
                    ->update(['quantity' => $toPivot->quantity + $qty, 'updated_at' => now()]);
            } else {
                DB::table('item_location')->insert([
                    'item_id'     => $item->id,
                    'location_id' => $toLocId,
                    'quantity'    => $qty,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }

            // --- 3. Sinkronisasi current_fill kedua lokasi ---
            Location::syncFill($fromLocId);
            Location::syncFill($toLocId);

            // --- 4. Update status tugas menjadi completed ---
            $task->update([
                'status'       => 'completed',
                'completed_by' => auth()->id(),
                'completed_at' => now(),
            ]);
        });

        return redirect()->route('petugas.relocationTasks.index')
            ->with('success', "Barang berhasil dipindahkan! Lokasi stok telah diperbarui secara otomatis.");
    }
}
