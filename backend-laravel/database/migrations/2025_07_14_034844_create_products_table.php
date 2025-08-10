<?php

// database/migrations/2025_08_09_000001_create_products_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('publisher_id')->constrained('publishers')->cascadeOnUpdate()->restrictOnDelete();
            $table->unsignedInteger('stock');
            $table->decimal('price', 8, 2);
            $table->timestamps();

            $table->index('title');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
