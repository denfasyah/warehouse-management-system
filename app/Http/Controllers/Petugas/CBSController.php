<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Location;
use Illuminate\Http\Request;

class CBSController extends Controller
{
    public function locations()
    {
        $locations = Location::with('items')->orderBy('code')->get();
        
        // Group locations by zone for visualization
        $zones = $locations->groupBy('zone');
        
        return view('petugas.cbs.locations', compact('zones', 'locations'));
    }

    public function arrangement(Request $request)
    {
        $query = Item::query()->with('category', 'locations');

        if ($request->has('class') && $request->class !== '') {
            $query->where('storage_class', $request->class);
        }

        $items = $query->paginate(20)->appends($request->all());

        return view('petugas.cbs.arrangement', compact('items'));
    }
}
