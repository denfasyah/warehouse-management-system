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
        $query = Item::with(['category', 'location'])->latest();

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

        // Update fill dari location (bukan dari total qty barang, tapi "jumlah macam barang/SKU" di rak)
        // Atau jika sistem Anda menghitung current_fill berdasarkan stock barang:
        // $location = Location::find($data['location_id']);
        // $location->increment('current_fill', 1);

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
        
        $item->update($data);

        return redirect()->route('admin.items.index')->with('success', 'Data barang berhasil diperbarui.');
    }

    public function destroy(Item $item)
    {
        if ($item->stock > 0) {
            return back()->with('error', 'Barang tidak dapat dihapus karena masih ada stok.');
        }

        $item->delete();
        return redirect()->route('admin.items.index')->with('success', 'Data barang berhasil dihapus.');
    }

    public function exportCsv(Request $request)
    {
        $fileName = 'data_barang_wms_' . date('Y-m-d_His') . '.csv';
        $items = Item::with(['category', 'location'])->get();

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
            fputcsv($file, $columns);

            foreach ($items as $item) {
                $row['Nama Barang'] = $item->name;
                $row['Kategori']    = $item->category->name ?? '-';
                $row['SKU']         = $item->sku;
                $row['Barcode']     = $item->barcode;
                $row['Lokasi/Rak']  = $item->location->code ?? '-';
                $row['Stok']        = $item->stock;
                $row['Satuan']      = $item->unit;
                $row['Min Stok']    = $item->min_stock;
                $row['Kelas CBS']   = strtoupper($item->storage_class);

                fputcsv($file, array($row['Nama Barang'], $row['Kategori'], $row['SKU'], $row['Barcode'], $row['Lokasi/Rak'], $row['Stok'], $row['Satuan'], $row['Min Stok'], $row['Kelas CBS']));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
