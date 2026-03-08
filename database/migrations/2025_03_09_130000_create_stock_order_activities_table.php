<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_order_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action'); // created, status_changed, charges_updated, received_to_inventory
            $table->text('description');
            $table->json('changes')->nullable(); // e.g. {"amount_paid":{"old":100,"new":80}, "status":{"old":"ordered","new":"in_transit"}}
            $table->timestamp('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_order_activities');
    }
};
