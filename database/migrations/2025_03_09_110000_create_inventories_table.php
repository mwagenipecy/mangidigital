<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->decimal('quantity', 14, 2)->default(0);
            $table->decimal('price_per_unit', 14, 2)->nullable();
            $table->boolean('is_out_of_stock')->default(false);
            $table->timestamps();

            $table->unique(['organization_id', 'product_id', 'store_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
