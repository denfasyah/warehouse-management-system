<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Milon\Barcode\DNS1D;

class BarcodeController extends Controller
{
    public function print(Item $item)
    {
        $barcodeGenerator = new DNS1D();
        
        // Generate SVG
        $barcodeHtml = $barcodeGenerator->getBarcodeHTML($item->sku, 'C128', 2, 60, 'black', true);
        
        return view('admin.items.print-barcode', compact('item', 'barcodeHtml'));
    }
}
