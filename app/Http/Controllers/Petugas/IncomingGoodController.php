<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\IncomingGood;
use App\Models\Item;
use App\Http\Requests\Petugas\IncomingGoodRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class IncomingGoodController extends Controller
{
    public function index()
    {
        // Tampilkan histori masuk hari ini/bulan ini milik petugas ini
        $incomingGoods = IncomingGood::with(['item.category', 'location', 'user'])
                            ->where('user_id', auth()->id())
                            ->latest()
                            ->paginate(20);
                            
        return view('petugas.incoming.index', compact('incomingGoods'));
    }

    public function create()
    {
        return view('petugas.incoming.create');
    }

    public function store(IncomingGoodRequest $request)
    {
        try {
            DB::beginTransaction();

            $item = Item::with('locations')->findOrFail($request->item_id);
            
            // Gunakan lokasi picking pertama (bukan BLK/bulk) sebagai lokasi catat barang masuk
            $pickingLocation = $item->locations->firstWhere('zone', '!=', 'BLK');
            $locationId = $pickingLocation ? $pickingLocation->id : ($item->locations->first()?->id);

            if (!$locationId) {
                return back()->with('error', 'Barang ini belum memiliki lokasi penyimpanan yang terdaftar.')->withInput();
            }

            $location = $item->locations->find($locationId);
            // Cek kapasitas hanya untuk picking zone (bukan bulk)
            if ($location && !$location->is_bulk_zone && ($location->current_fill + $request->quantity > $location->capacity)) {
                return back()->with('error', 'Kapasitas rak (' . $location->code . ') tidak mencukupi. Sisa ruang: ' . ($location->capacity - $location->current_fill))->withInput();
            }

            // Catat history masuk
            IncomingGood::create([
                'item_id' => $item->id,
                'user_id' => auth()->id(),
                'location_id' => $locationId,
                'quantity' => $request->quantity,
                'note' => $request->note,
            ]);

            // Update stok item
            $item->increment('stock', $request->quantity);

            // Update pivot quantity (quantity di lokasi picking bertambah)
            $currentQty = \Illuminate\Support\Facades\DB::table('item_location')
                ->where('item_id', $item->id)
                ->where('location_id', $locationId)
                ->value('quantity') ?? 0;
            
            \Illuminate\Support\Facades\DB::table('item_location')
                ->where('item_id', $item->id)
                ->where('location_id', $locationId)
                ->update(['quantity' => $currentQty + $request->quantity]);

            // Sinkronkan current_fill lokasi dari pivot (satu titik terpusat)
            \App\Models\Location::syncFill($locationId);

            DB::commit();

            // Hitung ulang CBS untuk item ini (meskipun incoming tidak pengaruhi score, memastikan up-to-date)
            \App\Services\CBSService::recalculate($item);

            return redirect()->route('petugas.incoming.index')->with('success', 'Barang masuk berhasil dicatat dan stok ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error recording incoming good: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan sistem saat memproses barang masuk.')->withInput();
        }
    }

    // Endpoint untuk Live Search (AJAX)
    public function search(Request $request)
    {
        $query = strtolower($request->get('q'));
        
        if (empty($query)) {
            return response()->json([]);
        }

        $items = Item::with(['category', 'locations'])
            ->whereRaw('LOWER(name) LIKE ?', ["%{$query}%"])
            ->orWhereRaw('LOWER(sku) LIKE ?', ["%{$query}%"])
            ->orWhereRaw('LOWER(barcode) LIKE ?', ["%{$query}%"])
            ->limit(10)
            ->get()
            ->map(function($item) {
                // Tambahkan field ringkas locations_codes agar mudah dibaca di JS
                $item->locations_codes = $item->locations->pluck('code')->join(', ');
                return $item;
            });

        return response()->json($items);
    }
}
