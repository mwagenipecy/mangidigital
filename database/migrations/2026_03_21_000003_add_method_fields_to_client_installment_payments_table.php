<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_installment_payments', function (Blueprint $table) {
            $table->string('payment_method', 30)->default('cash')->after('amount'); // cash, bank, mobile_wallet
            $table->string('payment_reference', 120)->nullable()->after('payment_method');
        });
    }

    public function down(): void
    {
        Schema::table('client_installment_payments', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'payment_reference']);
        });
    }
};
