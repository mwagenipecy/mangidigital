<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cargo_shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->uuid('logistics_flow_token')->unique();
            $table->string('reference_number');
            $table->string('client_name');
            $table->string('client_phone', 50);
            $table->string('client_email')->nullable();
            $table->text('cargo_description')->nullable();
            $table->foreignId('delivery_service_provider_id')->nullable()->constrained('service_providers')->nullOnDelete();
            $table->string('delivery_status', 32)->default('pending');
            $table->text('delivery_pickup_office')->nullable();
            $table->timestamp('delivery_dispatched_at')->nullable();
            $table->timestamp('delivery_arrived_at')->nullable();
            $table->timestamp('delivery_received_at')->nullable();
            $table->decimal('delivery_cost', 14, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['organization_id', 'delivery_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cargo_shipments');
    }
};
