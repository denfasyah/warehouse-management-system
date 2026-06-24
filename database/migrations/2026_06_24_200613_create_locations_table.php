<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('zone', 5);         // "A", "B", "C", "D"
            $table->string('rack', 5);          // "01", "02", ...
            $table->string('bin', 5);           // "01", "02" (sub-posisi/level)
            $table->string('code', 20)->unique(); // "A-01-01" auto-generated
            $table->enum('storage_class', ['fast', 'medium', 'slow', 'general'])->default('general');
            $table->unsignedInteger('capacity')->default(100); // kapasitas maks (unit item)
            $table->unsignedInteger('current_fill')->default(0); // isi saat ini
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
