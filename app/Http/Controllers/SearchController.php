<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Location;

class SearchController extends Controller
{
    /**
     * Menangani pencarian global (Item & Location)
     */
    public function index(Request $request)
    {
        $query = trim($request->input('q'));
        
        if (empty($query)) {
            return back()->with('error', 'Masukkan kata kunci pencarian.');
        }

        // Pencarian Barang (SKU, Name, Barcode)
        $items = Item::with(['category', 'locations'])
            ->where('name', 'LIKE', "%{$query}%")
            ->orWhere('sku', 'LIKE', "%{$query}%")
            ->orWhere('barcode', 'LIKE', "%{$query}%")
            ->get();

        // Pencarian Lokasi Rak (Code, Zone)
        $locations = Location::with('items')
            ->where('code', 'LIKE', "%{$query}%")
            ->orWhere('zone', 'LIKE', "%{$query}%")
            ->get();

        return view('search.results', compact('items', 'locations', 'query'));
    }
}
