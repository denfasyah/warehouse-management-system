<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class CBSController extends Controller
{
    public function classification(Request $request)
    {
        $query = Item::query()->with('category', 'locations');

        if ($request->has('class') && $request->class !== '') {
            $query->where('storage_class', $request->class);
        }

        $items = $query->paginate(20)->appends($request->all());

        return view('admin.cbs.classification', compact('items'));
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
}
