<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('order_type'); // international, local
            $table->foreignId('service_provider_id')->nullable()->constrained('service_providers')->nullOnDelete();
            $table->decimal('amount_paid', 14, 2)->default(0);
            $table->decimal('transport_charges', 14, 2)->default(0);
            $table->decimal('other_charges', 14, 2)->default(0);
            $table->date('payment_date')->nullable();
            $table->date('estimated_receive_date')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->string('status')->default('ordered'); // ordered, in_transit, received
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_orders');
    }
};
