<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;

class ScannerController extends Controller
{
    public function index()
    {
        return view('petugas.scanner.index');
    }

    // Endpoint API untuk Scanner mencari data barang by Barcode/SKU
    public function scan(Request $request)
    {
        $code = $request->get('code');

        if (empty($code)) {
            return response()->json(['success' => false, 'message' => 'Kode kosong.'], 400);
        }

        $item = Item::with(['category', 'location'])
                    ->where('barcode', $code)
                    ->orWhere('sku', $code)
                    ->first();

        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Barang tidak ditemukan.'], 404);
        }

        return response()->json([
            'success' => true,
            'item' => [
                'id' => $item->id,
                'name' => $item->name,
                'sku' => $item->sku,
                'barcode' => $item->barcode,
                'stock' => $item->stock,
                'unit' => $item->unit,
                'category_name' => $item->category->name ?? '-',
                'location_code' => $item->location->code ?? '-',
            ]
        ]);
    }
}
