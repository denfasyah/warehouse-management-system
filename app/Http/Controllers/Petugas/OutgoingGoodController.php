<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\OutgoingGood;
use App\Models\Item;
use App\Http\Requests\Petugas\OutgoingGoodRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OutgoingGoodController extends Controller
{
    public function index()
    {
        // Tampilkan histori request keluar hari ini/bulan ini milik petugas ini
        $outgoingGoods = OutgoingGood::with(['item.category', 'location', 'requestedBy', 'approvedBy'])
                            ->where('requested_by', auth()->id())
                            ->latest()
                            ->paginate(20);
                            
        return view('petugas.outgoing.index', compact('outgoingGoods'));
    }

    public function create()
    {
        return view('petugas.outgoing.create');
    }

    public function store(OutgoingGoodRequest $request)
    {
        try {
            DB::beginTransaction();

            $item = Item::findOrFail($request->item_id);
            
            // Validasi: Qty tidak boleh melebihi stok yang ada
            if ($request->quantity > $item->stock) {
                return back()->with('error', "Kuantitas keluar ({$request->quantity}) melebihi sisa stok saat ini ({$item->stock}).")->withInput();
            }

            // Catat request keluar dengan status 'pending' (Default)
            // Stok BELUM dikurangi di tahap ini.
            OutgoingGood::create([
                'item_id' => $item->id,
                'requested_by' => auth()->id(),
                'location_id' => $item->location_id,
                'quantity' => $request->quantity,
                'destination' => $request->destination,
                'note' => $request->note,
                'status' => 'pending'
            ]);

            DB::commit();

            return redirect()->route('petugas.outgoing.index')->with('success', 'Permintaan Barang Keluar berhasil dikirim. Menunggu persetujuan Admin.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error recording outgoing good request: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan sistem saat memproses permintaan barang keluar.')->withInput();
        }
    }
}
