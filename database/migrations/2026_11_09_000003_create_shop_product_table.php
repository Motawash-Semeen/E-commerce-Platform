<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A shop's listing of a catalog product (model: ShopProduct). Price, description
 * and thumbnail live here, so each shop sells the same product its own way.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shop_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained()->cascadeOnDelete();      // shop deleted = its listings go too
            $table->foreignId('product_id')->constrained()->restrictOnDelete();  // a listed product can't be deleted
            $table->decimal('price', 10, 2);
            $table->decimal('old_price', 10, 2)->nullable();
            $table->text('description')->nullable();   // overrides the catalog description
            $table->string('thumbnail')->nullable();   // overrides the catalog image
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['shop_id', 'product_id']); // a product is listed at most once per shop
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_product');
    }
};
