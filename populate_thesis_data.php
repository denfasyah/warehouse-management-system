<?php

use App\Models\Item;
use App\Models\Category;
use App\Models\OutgoingGood;
use Illuminate\Support\Facades\DB;
use App\Services\CBSService;

// Create or find a default category
$category = Category::firstOrCreate(
    ['name' => 'Automotive Parts'],
    ['code' => 'AUTO', 'description' => 'Automotive Spare Parts', 'is_active' => true]
);

// Excel Data
$data = [
    ['name' => 'Engine Oil', 'stock' => 56293, 'sales' => 42150],
    ['name' => 'Battery', 'stock' => 4821, 'sales' => 3250],
    ['name' => 'Brake Pad', 'stock' => 4655, 'sales' => 2800],
    ['name' => 'Transmisi Oil', 'stock' => 3241, 'sales' => 1950],
    ['name' => 'Brake Fluid', 'stock' => 1044, 'sales' => 650],
    ['name' => 'Brake Disc', 'stock' => 967, 'sales' => 420],
    ['name' => 'Gear Oil', 'stock' => 624, 'sales' => 310],
];

DB::beginTransaction();
try {
    // Optional: Wipe existing data if you want a clean slate (uncomment if needed)
    // OutgoingGood::truncate();
    // Item::truncate();

    // Default admin user for outgoing goods
    $adminId = 1; // Assuming user ID 1 is admin

    foreach ($data as $row) {
        // Create Item
        $item = Item::create([
            'category_id' => $category->id,
            'name' => $row['name'],
            'stock' => $row['stock'],
            'unit' => 'pcs',
            'min_stock' => 10,
            'sku' => Item::generateSku($category->code),
            'barcode' => Item::generateBarcode(),
        ]);

        // Create Outgoing Good History to match sales (approved, processed recently)
        OutgoingGood::create([
            'item_id' => $item->id,
            'requested_by' => $adminId,
            'approved_by' => $adminId,
            'quantity' => $row['sales'],
            'status' => 'approved',
            'processed_at' => now()->subDays(2), // 2 days ago, within the 30 days window
            'note' => 'Thesis data generation',
        ]);
    }

    // Run recalculation to apply CBS logic based on the new data
    CBSService::recalculateAll();

    DB::commit();
    echo "Thesis data populated successfully!\n";
} catch (\Exception $e) {
    DB::rollBack();
    echo "Error: " . $e->getMessage() . "\n";
}
