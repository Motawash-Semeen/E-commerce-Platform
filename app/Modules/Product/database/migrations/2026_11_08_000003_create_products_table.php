<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The shared catalog. Prices here are the catalog's suggested prices;
 * each shop sets its own price in shop_product.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->foreignId('subcategory_id')->constrained()->restrictOnDelete();
            // Who added it to the catalog (admin or a shop owner). Kept if that user is deleted.
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name', 150);
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('image');                         // path on the public disk
            $table->decimal('old_price', 10, 2)->nullable(); // optional "was" price
            $table->decimal('new_price', 10, 2);             // suggested selling price
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
