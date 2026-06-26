<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Location;
use App\Models\RelocationTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class CBSController extends Controller
{
    public function classification(Request $request)
    {
        $query = Item::query()->with('category', 'locations');

        if ($request->has('class') && $request->class !== '') {
            $query->where('storage_class', $request->class);
        }

        $items = $query->paginate(20)->appends($request->all());

        // Jumlah tugas relokasi yang masih pending
        $pendingRelocationCount = RelocationTask::pending()->count();

        return view('admin.cbs.classification', compact('items', 'pendingRelocationCount'));
    }

    public function recalculate()
    {
        Artisan::call('cbs:calculate');
        
        return redirect()->route('admin.cbs.classification')
            ->with('success', 'Kalkulasi Class-Based Storage (CBS) berhasil dijalankan.');
    }

    public function mapping()
    {
        $locations = Location::with('items')->orderBy('code')->get();
        
        // Group locations by zone for visualization
        $zones = $locations->groupBy('zone');
        
        return view('admin.cbs.mapping', compact('zones', 'locations'));
    }

    /**
     * Generate Tugas Relokasi untuk semua barang yang kelas CBS-nya tidak sesuai lokasi.
     * Menggantikan fungsi "Auto Assign" yang dulu langsung memindah data.
     */
    public function generateRelocationTasks()
    {
        $items = Item::with('locations')->get()->filter(fn($item) => $item->is_location_mismatch);

        $generatedCount = 0;

        DB::transaction(function () use ($items, &$generatedCount) {
            foreach ($items as $item) {
                // Cek apakah sudah ada tugas pending untuk item ini (jangan dobel)
                $alreadyPending = RelocationTask::pending()->where('item_id', $item->id)->exists();
                if ($alreadyPending) {
                    continue;
                }

                $suggestions = \App\Services\CBSService::suggestLocations($item);
                if ($suggestions->isEmpty()) {
                    continue;
                }

                $targetLocation = $suggestions->first();

                // Buat satu tugas per lokasi asal yang tidak sesuai
                foreach ($item->locations as $currentLoc) {
                    if ($currentLoc->storage_class === 'general' || $currentLoc->is_bulk_zone) {
                        continue;
                    }
                    if ($currentLoc->storage_class !== $item->storage_class) {
                        RelocationTask::create([
                            'item_id'          => $item->id,
                            'from_location_id' => $currentLoc->id,
                            'to_location_id'   => $targetLocation->id,
                            'quantity'         => $currentLoc->pivot->quantity,
                            'status'           => 'pending',
                        ]);
                        $generatedCount++;
                    }
                }
            }
        });

        return redirect()->route('admin.cbs.classification')
            ->with('success', "Berhasil membuat {$generatedCount} Tugas Relokasi. Petugas akan mendapatkan notifikasi untuk memindahkan barang secara fisik.");
    }

    /**
     * Tampilkan halaman daftar semua Tugas Relokasi (untuk Admin memantau progres).
     */
    public function relocationTasks()
    {
        $tasks = RelocationTask::with('item', 'fromLocation', 'toLocation', 'completedBy')
            ->latest()
            ->paginate(20);

        $pendingCount   = RelocationTask::pending()->count();
        $completedCount = RelocationTask::completed()->count();

        return view('admin.cbs.relocation_tasks', compact('tasks', 'pendingCount', 'completedCount'));
    }

    /**
     * Batalkan tugas relokasi
     */
    public function cancelTask(RelocationTask $task)
    {
        $task->update(['status' => 'cancelled']);
        return redirect()->back()->with('success', 'Tugas relokasi dibatalkan.');
    }
}
