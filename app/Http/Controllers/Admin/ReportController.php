<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Location;
use App\Models\IncomingGood;
use App\Models\OutgoingGood;
use App\Models\Category;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function stockReport(Request $request)
    {
        $query = Item::with(['category', 'locations']);
        
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }
        
        if ($request->has('low_stock') && $request->low_stock == '1') {
            $query->whereRaw('stock <= min_stock');
        }

        $items = $query->get();
        $categories = Category::all();

        return view('admin.reports.stock', compact('items', 'categories'));
    }

    public function exportStockPdf(Request $request)
    {
        $query = Item::with(['category', 'locations']);
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->has('low_stock') && $request->low_stock == '1') {
            $query->whereRaw('stock <= min_stock');
        }
        $items = $query->get();

        $pdf = Pdf::loadView('admin.reports.pdf.stock-pdf', compact('items'));
        return $pdf->download('laporan-stok.pdf');
    }

    public function incomingReport(Request $request)
    {
        $query = IncomingGood::with(['item', 'user', 'location']);
        
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }

        $incomings = $query->latest()->get();
        return view('admin.reports.incoming', compact('incomings'));
    }

    public function exportIncomingPdf(Request $request)
    {
        $query = IncomingGood::with(['item', 'user', 'location']);
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }
        $incomings = $query->latest()->get();

        $pdf = Pdf::loadView('admin.reports.pdf.incoming-pdf', compact('incomings'));
        return $pdf->download('laporan-barang-masuk.pdf');
    }

    public function outgoingReport(Request $request)
    {
        $query = OutgoingGood::with(['item', 'requestedBy', 'approvedBy', 'location']);
        
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $outgoings = $query->latest()->get();
        return view('admin.reports.outgoing', compact('outgoings'));
    }

    public function exportOutgoingPdf(Request $request)
    {
        $query = OutgoingGood::with(['item', 'requestedBy', 'approvedBy', 'location']);
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        $outgoings = $query->latest()->get();

        $pdf = Pdf::loadView('admin.reports.pdf.outgoing-pdf', compact('outgoings'));
        return $pdf->download('laporan-barang-keluar.pdf');
    }

    public function storageReport(Request $request)
    {
        // Sync current_fill dari pivot agar data akurat sebelum ditampilkan
        $locationIds = \App\Models\Location::pluck('id')->toArray();
        \App\Models\Location::syncFill($locationIds);

        $query = Location::with(['items' => function($q) {
            $q->withPivot('quantity');
        }]);

        if ($request->zone) {
            $query->where('zone', $request->zone);
        }
        if ($request->status === 'over') {
            $query->whereColumn('current_fill', '>', 'capacity');
        } elseif ($request->status === 'full') {
            $query->whereColumn('current_fill', '>=', 'capacity');
        } elseif ($request->status === 'empty') {
            $query->where('current_fill', 0);
        }

        $locations = $query->orderBy('zone')->orderBy('code')->get();
        $zones     = Location::distinct()->pluck('zone')->sort()->values();

        return view('admin.reports.storage', compact('locations', 'zones'));
    }

    public function exportStoragePdf(Request $request)
    {
        $locationIds = \App\Models\Location::pluck('id')->toArray();
        \App\Models\Location::syncFill($locationIds);
        $locations = Location::with(['items' => fn($q) => $q->withPivot('quantity')])
            ->orderBy('zone')->orderBy('code')->get();
        $pdf = Pdf::loadView('admin.reports.pdf.storage-pdf', compact('locations'));
        return $pdf->download('laporan-storage.pdf');
    }
}
