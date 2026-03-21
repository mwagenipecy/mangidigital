<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->uuid('logistics_flow_token')->nullable()->unique()->after('receipt_number');
        });

        $sales = DB::table('sales')->where('delivery_requested', true)->whereNull('logistics_flow_token')->get(['id']);
        foreach ($sales as $row) {
            DB::table('sales')->where('id', $row->id)->update(['logistics_flow_token' => (string) Str::uuid()]);
        }
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropUnique(['logistics_flow_token']);
            $table->dropColumn('logistics_flow_token');
        });
    }
};
