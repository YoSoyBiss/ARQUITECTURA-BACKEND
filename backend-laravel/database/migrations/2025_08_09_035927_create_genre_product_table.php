<?php

// database/migrations/2025_08_09_000005_create_genre_product_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('genre_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('genre_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->unique(['genre_id','product_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('genre_product');
    }
};
