<?php


// database/migrations/2025_08_09_000100_create_product_supplier_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_supplier', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                  ->constrained('products')
                  ->cascadeOnDelete();              // si se borra el producto, se borra su costo
            $table->decimal('precio_proveedor', 10, 2)->default(0);
            $table->timestamps();

            $table->unique('product_id');          // 1 a 1 (un registro por producto)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_supplier');
    }
};
