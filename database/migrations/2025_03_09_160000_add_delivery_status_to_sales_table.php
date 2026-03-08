<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->string('delivery_status')->nullable()->after('delivery_service_provider_id');
            $table->timestamp('delivery_dispatched_at')->nullable()->after('delivery_status');
            $table->timestamp('delivery_arrived_at')->nullable()->after('delivery_dispatched_at');
            $table->timestamp('delivery_received_at')->nullable()->after('delivery_arrived_at');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['delivery_status', 'delivery_dispatched_at', 'delivery_arrived_at', 'delivery_received_at']);
        });
    }
};
