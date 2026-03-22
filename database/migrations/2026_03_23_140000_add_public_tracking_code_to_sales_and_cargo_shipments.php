<?php

use App\Models\CargoShipment;
use App\Models\Sale;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->string('public_tracking_code', 32)->nullable()->unique()->after('logistics_flow_token');
        });

        Schema::table('cargo_shipments', function (Blueprint $table) {
            $table->string('public_tracking_code', 32)->nullable()->unique()->after('logistics_flow_token');
        });

        Sale::query()
            ->whereNotNull('logistics_flow_token')
            ->whereNull('public_tracking_code')
            ->orderBy('id')
            ->chunkById(100, function ($sales): void {
                foreach ($sales as $sale) {
                    $sale->forceFill([
                        'public_tracking_code' => Sale::generateUniquePublicTrackingCode(),
                    ])->saveQuietly();
                }
            });

        CargoShipment::query()
            ->whereNull('public_tracking_code')
            ->orderBy('id')
            ->chunkById(100, function ($cargos): void {
                foreach ($cargos as $cargo) {
                    $cargo->forceFill([
                        'public_tracking_code' => CargoShipment::generateUniquePublicTrackingCode(),
                    ])->saveQuietly();
                }
            });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropUnique(['public_tracking_code']);
            $table->dropColumn('public_tracking_code');
        });

        Schema::table('cargo_shipments', function (Blueprint $table) {
            $table->dropUnique(['public_tracking_code']);
            $table->dropColumn('public_tracking_code');
        });
    }
};
