<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('products')) {
            if (Schema::hasColumn('products', 'author')) {
                Schema::table('products', function (Blueprint $table) {
                    $table->dropColumn('author');
                });
            }
            if (Schema::hasColumn('products', 'publisher')) {
                Schema::table('products', function (Blueprint $table) {
                    $table->dropColumn('publisher');
                });
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('products')) {
            if (!Schema::hasColumn('products', 'author')) {
                Schema::table('products', function (Blueprint $table) {
                    $table->string('author')->nullable();
                });
            }
            if (!Schema::hasColumn('products', 'publisher')) {
                Schema::table('products', function (Blueprint $table) {
                    $table->string('publisher')->nullable();
                });
            }
        }
    }
};
