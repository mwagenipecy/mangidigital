<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->string('client_name')->nullable();
            $table->string('client_phone')->nullable();
            $table->date('sale_date');
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->boolean('delivery_requested')->default(false);
            $table->decimal('delivery_cost', 14, 2)->default(0);
            $table->foreignId('delivery_service_provider_id')->nullable()->constrained('service_providers')->nullOnDelete();
            $table->decimal('total', 14, 2)->default(0);
            $table->string('receipt_number')->nullable()->unique();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
