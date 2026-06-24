<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique()->nullable();               // slug untuk URL (nullable for migration safety)
            $table->string('sku', 50)->unique();          // kode unik sistem
            $table->string('barcode', 100)->unique()->nullable();      // nilai barcode Code128 (nullable, auto-generated)
            $table->string('barcode_image')->nullable();   // path file PNG di storage
            $table->string('unit', 20)->default('pcs');    // pcs, box, kg, liter, dll
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedInteger('min_stock')->default(5); // ambang batas stok rendah
            $table->enum('storage_class', ['fast', 'medium', 'slow', 'unclassified'])->default('unclassified');
            $table->unsignedInteger('frequency_score')->default(0); // total keluar 30 hari (diisi CBS engine)
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
