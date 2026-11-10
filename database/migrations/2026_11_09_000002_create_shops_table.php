<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Minimal shops table: one shop per shop owner. The shops task adds the
 * admin screens (and any extra columns) on top of this.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            // unique = an owner has at most one shop; restrict = an owner with a shop can't be deleted.
            $table->foreignId('user_id')->unique()->constrained()->restrictOnDelete();
            $table->string('name', 150);
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};
