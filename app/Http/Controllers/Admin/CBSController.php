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
            $virtualFills = []; // Untuk melacak kapasitas virtual selama batch ini
            
            foreach ($items as $item) {
                // Cek apakah sudah ada tugas pending untuk item ini (jangan dobel)
                $alreadyPending = RelocationTask::pending()->where('item_id', $item->id)->exists();
                if ($alreadyPending) {
                    continue;
                }

                // Buat tugas untuk setiap lokasi asal yang tidak sesuai
                foreach ($item->locations as $currentLoc) {
                    if ($currentLoc->storage_class === 'general' || $currentLoc->is_bulk_zone) {
                        continue; // Biarkan kalau sudah di BLK/General
                    }
                    if ($currentLoc->storage_class !== $item->storage_class) {
                        $qtyToMove = $currentLoc->pivot->quantity;

                        while ($qtyToMove > 0) {
                            // Cari lokasi tujuan yang kelasnya sesuai dan MASIH ADA sisa kapasitas
                            $targetLocs = Location::where('storage_class', $item->storage_class)
                                ->where('id', '!=', $currentLoc->id)
                                ->get();
                                
                            $foundTarget = null;
                            $spaceAvailable = 0;
                            
                            foreach($targetLocs as $tl) {
                                $currentFill = $virtualFills[$tl->id] ?? $tl->current_fill;
                                $space = $tl->capacity - $currentFill;
                                if ($space > 0) {
                                    $foundTarget = $tl;
                                    $spaceAvailable = $space;
                                    break;
                                }
                            }
                            
                            // Jika semua lokasi kelas tersebut penuh, lempar ke BLK/General
                            if (!$foundTarget) {
                                $fallbackLocs = Location::where('storage_class', 'general')
                                    ->orWhere('zone', 'BLK')
                                    ->get();
                                foreach($fallbackLocs as $fl) {
                                    $currentFill = $virtualFills[$fl->id] ?? $fl->current_fill;
                                    $space = $fl->capacity - $currentFill;
                                    if ($space > 0) {
                                        $foundTarget = $fl;
                                        $spaceAvailable = $space;
                                        break;
                                    }
                                }
                            }
                            
                            // Jika gudang benar-benar full 100%, terpaksa tumpuk di BLK
                            if (!$foundTarget) {
                                $foundTarget = Location::where('zone', 'BLK')->first();
                                $spaceAvailable = $qtyToMove; // paksa semuanya ke sini
                            }
                            
                            $qtyForThisTask = min($qtyToMove, $spaceAvailable);
                            
                            RelocationTask::create([
                                'item_id'          => $item->id,
                                'from_location_id' => $currentLoc->id,
                                'to_location_id'   => $foundTarget->id,
                                'quantity'         => $qtyForThisTask,
                                'status'           => 'pending',
                            ]);
                            $generatedCount++;
                            
                            // Catat virtual fill agar task berikutnya tidak mengisi tempat yang sudah di-booking
                            if (!isset($virtualFills[$foundTarget->id])) {
                                $virtualFills[$foundTarget->id] = $foundTarget->current_fill;
                            }
                            $virtualFills[$foundTarget->id] += $qtyForThisTask;
                            
                            $qtyToMove -= $qtyForThisTask;
                        }
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
