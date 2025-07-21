<?php

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_supplier', function (Blueprint $table) {
            $table->id();

            // Reference to the product (MySQL)
            $table->foreignId('product_id')->constrained('productos')->onDelete('cascade');

            // Reference to the supplier (stored as MongoDB _id)
            $table->string('supplier_mongo_id'); // MongoDB user _id

            // Optional: purchase price from that supplier
            $table->decimal('purchase_price', 8, 2)->nullable();

            $table->timestamps();

            // Prevent duplicates for the same product/supplier pair
            $table->unique(['product_id', 'supplier_mongo_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_supplier');
    }
};
