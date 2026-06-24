<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('type'); // approval_request | approved | rejected | low_stock | reclassified
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable(); // payload: { outgoing_id, item_id, dll }
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        // Tabel settings untuk threshold CBS dan konfigurasi lain
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('value');
            $table->string('label')->nullable(); // label ramah pengguna
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('settings');
    }
};
