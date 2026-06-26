<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ItemRequest;
use App\Models\Item;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::with(['category', 'locations'])->latest();

        // Pencarian
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter Kategori
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $items = $query->paginate(15)->withQueryString();
        $categories = Category::where('is_active', true)->get();

        return view('admin.items.index', compact('items', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        // Hanya ambil lokasi yang aktif dan kapasitas belum penuh
        $locations = Location::where('is_active', true)
            ->whereRaw('current_fill < capacity')
            ->get();

        return view('admin.items.create', compact('categories', 'locations'));
    }

    public function store(ItemRequest $request)
    {
        $data = $request->validated();
        
        $item = Item::create($data);
        
        if (!empty($data['location_ids'])) {
            $newLocationIds = array_map('intval', $data['location_ids']);
            
            // Ambil detail lokasi yang dipilih, pastikan bulk zone diproses TERAKHIR
            $selectedLocations = Location::whereIn('id', $newLocationIds)
                ->orderByRaw("zone = 'BLK' ASC") // non-bulk (0) didahulukan, baru bulk (1)
                ->orderBy('zone')
                ->orderBy('rack')
                ->get();
            
            // Distribusikan stok awal item ke lokasi-lokasi secara berurutan (sesuai kapasitas)
            $remainingStock = $item->stock;
            $syncData = [];
            
            foreach ($selectedLocations as $loc) {
                if ($remainingStock <= 0) {
                    $syncData[$loc->id] = ['quantity' => 0];
                    continue;
                }
                
                // Untuk zona Bulk: tampung semua sisa stok
                if ($loc->is_bulk_zone) {
                    $syncData[$loc->id] = ['quantity' => $remainingStock];
                    $remainingStock = 0;
                } else {
                    // Zona Picking: hitung slot tersisa (dari barang LAIN, tapi ini item baru jadi tidak ada item ini di loc tsb)
                    $otherItemsQty = \Illuminate\Support\Facades\DB::table('item_location')
                        ->where('location_id', $loc->id)
                        ->sum('quantity');
                    
                    $availableSlot = max(0, $loc->capacity - $otherItemsQty);
                    $qtyForThisLoc = min($remainingStock, $availableSlot);
                    
                    $syncData[$loc->id] = ['quantity' => $qtyForThisLoc];
                    $remainingStock -= $qtyForThisLoc;
                }
            }
            
            $item->locations()->attach($syncData);
            
            // Sinkronkan current_fill untuk lokasi yang baru dihuni
            Location::syncFill($newLocationIds);
        }

        return redirect()->route('admin.items.index')->with('success', 'Barang baru berhasil ditambahkan.');
    }

    public function edit(Item $item)
    {
        $categories = Category::where('is_active', true)->get();
        $locations = Location::where('is_active', true)->get(); // Tetap ambil semua, agar lokasi saat ini masih muncul
        
        return view('admin.items.edit', compact('item', 'categories', 'locations'));
    }

    public function update(ItemRequest $request, Item $item)
    {
        $data = $request->validated();
        
        // Catat lokasi LAMA sebelum sync (agar bisa direkalkuasi juga)
        $oldLocationIds = $item->locations()->pluck('locations.id')->toArray();

        $item->update($data);
        
        if (isset($data['location_ids'])) {
            $newLocationIds = array_map('intval', $data['location_ids']);
            
            // Ambil detail lokasi yang dipilih, pastikan bulk zone diproses TERAKHIR
            $selectedLocations = Location::whereIn('id', $newLocationIds)
                ->orderByRaw("zone = 'BLK' ASC") // non-bulk (0) didahulukan, baru bulk (1)
                ->orderBy('zone')
                ->orderBy('rack')
                ->get();
            
            // Distribusikan stok item ke lokasi-lokasi secara berurutan (sesuai kapasitas)
            $remainingStock = $item->stock;
            $syncData = [];
            
            foreach ($selectedLocations as $loc) {
                if ($remainingStock <= 0) {
                    // Tidak ada stok tersisa → lokasi ini kosong untuk item ini
                    $syncData[$loc->id] = ['quantity' => 0];
                    continue;
                }
                
                // Untuk zona Bulk: tampung semua sisa stok
                if ($loc->is_bulk_zone) {
                    $syncData[$loc->id] = ['quantity' => $remainingStock];
                    $remainingStock = 0;
                } else {
                    // Zona Picking: hitung berapa slot yang tersisa di rak ini (dari barang LAIN)
                    $otherItemsQty = \Illuminate\Support\Facades\DB::table('item_location')
                        ->where('location_id', $loc->id)
                        ->where('item_id', '!=', $item->id)
                        ->sum('quantity');
                    
                    $availableSlot = max(0, $loc->capacity - $otherItemsQty);
                    $qtyForThisLoc = min($remainingStock, $availableSlot);
                    
                    $syncData[$loc->id] = ['quantity' => $qtyForThisLoc];
                    $remainingStock -= $qtyForThisLoc;
                }
            }
            
            // Sync lokasi dengan distribusi baru
            $item->locations()->sync($syncData);
            
            // Sinkronkan current_fill untuk lokasi lama DAN baru
            $allAffectedIds = array_unique(array_merge($oldLocationIds, $newLocationIds));
            Location::syncFill($allAffectedIds);
        } elseif (!empty($oldLocationIds)) {
            Location::syncFill($oldLocationIds);
        }

        return redirect()->route('admin.items.index')->with('success', 'Data barang berhasil diperbarui.');
    }

    public function destroy(Item $item)
    {
        if ($item->stock > 0) {
            return back()->with('error', 'Barang tidak dapat dihapus karena masih ada stok.');
        }

        // Catat lokasi yang akan terdampak sebelum hapus
        $locationIds = $item->locations()->pluck('locations.id')->toArray();

        // Detach semua relasi lokasi dulu (cascade pivot)
        $item->locations()->detach();
        $item->delete();

        // Rekalkuasi current_fill untuk semua lokasi yang terdampak
        if (!empty($locationIds)) {
            Location::syncFill($locationIds);
        }

        return redirect()->route('admin.items.index')->with('success', 'Data barang berhasil dihapus.');
    }

    public function exportCsv(Request $request)
    {
        $fileName = 'data_barang_wms_' . date('Y-m-d_His') . '.csv';
        $items = Item::with(['category', 'locations'])->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Nama Barang', 'Kategori', 'SKU', 'Barcode', 'Lokasi/Rak', 'Stok', 'Satuan', 'Min Stok', 'Kelas CBS'];

        $callback = function() use($items, $columns) {
            $file = fopen('php://output', 'w');
            
            // Tambahkan BOM (Byte Order Mark) untuk UTF-8 agar Excel membaca karakter khusus dengan benar
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Gunakan separator titik koma (;) agar lebih kompatibel dengan Excel di region Indonesia/Eropa
            fputcsv($file, $columns, ';');

            foreach ($items as $item) {
                $row['Nama Barang'] = $item->name;
                $row['Kategori']    = $item->category->name ?? '-';
                $row['SKU']         = $item->sku;
                $row['Barcode']     = $item->barcode;
                $row['Lokasi/Rak']  = $item->locations->pluck('code')->join(', ') ?: '-';
                $row['Stok']        = $item->stock;
                $row['Satuan']      = $item->unit;
                $row['Min Stok']    = $item->min_stock;
                $row['Kelas CBS']   = strtoupper($item->storage_class);

                fputcsv($file, array($row['Nama Barang'], $row['Kategori'], $row['SKU'], $row['Barcode'], $row['Lokasi/Rak'], $row['Stok'], $row['Satuan'], $row['Min Stok'], $row['Kelas CBS']), ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
