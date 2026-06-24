<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LocationRequest;
use App\Models\Location;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::withCount('items')->latest()->get();
        return view('admin.locations.index', compact('locations'));
    }

    public function store(LocationRequest $request)
    {
        $data = $request->validated();
        
        // Pad rack and bin with leading zeros
        $data['rack'] = str_pad($data['rack'], 2, '0', STR_PAD_LEFT);
        $data['bin'] = str_pad($data['bin'], 2, '0', STR_PAD_LEFT);
        
        // Generate unique code
        $data['code'] = Location::generateCode($data['zone'], $data['rack'], $data['bin']);
        
        // Default current fill to 0
        $data['current_fill'] = 0;

        Location::create($data);
        return redirect()->route('admin.locations.index')->with('success', 'Lokasi/Rak berhasil ditambahkan.');
    }

    public function update(LocationRequest $request, Location $location)
    {
        $data = $request->validated();

        $data['rack'] = str_pad($data['rack'], 2, '0', STR_PAD_LEFT);
        $data['bin'] = str_pad($data['bin'], 2, '0', STR_PAD_LEFT);
        $data['code'] = Location::generateCode($data['zone'], $data['rack'], $data['bin']);

        if ($data['capacity'] < $location->current_fill) {
            return back()->with('error', 'Kapasitas tidak boleh lebih kecil dari jumlah barang yang ada saat ini (' . $location->current_fill . ').');
        }

        $location->update($data);
        return redirect()->route('admin.locations.index')->with('success', 'Lokasi/Rak berhasil diperbarui.');
    }

    public function destroy(Location $location)
    {
        if ($location->items()->count() > 0 || $location->current_fill > 0) {
            return back()->with('error', 'Lokasi tidak dapat dihapus karena masih terisi barang.');
        }

        $location->delete();
        return redirect()->route('admin.locations.index')->with('success', 'Lokasi/Rak berhasil dihapus.');
    }
}
