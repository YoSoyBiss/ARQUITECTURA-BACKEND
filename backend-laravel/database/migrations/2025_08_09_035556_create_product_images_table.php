<?php

// database/migrations/2025_08_09_000003_create_product_images_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('url');        // o path en storage
            $table->string('alt')->nullable();
            $table->boolean('is_main')->default(false);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('product_images');
    }
};
