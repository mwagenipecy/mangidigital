<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_payment_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('plan_name')->default('Client Plan');
            $table->decimal('goal_amount', 14, 2);
            $table->string('status')->default('open'); // open | closed
            $table->date('started_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamp('last_reminded_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_payment_plans');
    }
};
