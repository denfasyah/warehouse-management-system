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

            $item = Item::findOrFail($request->item_id);
            $locationId = $item->location_id; // Default menggunakan lokasi master item

            // Cek kapasitas
            $location = $item->location;
            if ($location->current_fill + $request->quantity > $location->capacity) {
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

            // Update isi rak
            $location->increment('current_fill', $request->quantity);

            DB::commit();

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
        $query = $request->get('q');
        
        if (empty($query)) {
            return response()->json([]);
        }

        $items = Item::with(['category', 'location'])
            ->where('name', 'like', "%{$query}%")
            ->orWhere('sku', 'like', "%{$query}%")
            ->orWhere('barcode', 'like', "%{$query}%")
            ->limit(10)
            ->get();

        return response()->json($items);
    }
}
