<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bravo_cars', function (Blueprint $table) {
            $table->string('transfer_service_address')->nullable()->after('address');
            $table->decimal('transfer_service_lat', 11, 8)->nullable()->after('transfer_service_address');
            $table->decimal('transfer_service_lng', 11, 8)->nullable()->after('transfer_service_lat');
            $table->decimal('transfer_service_radius_km', 8, 2)->nullable()->after('transfer_service_lng');
            $table->decimal('transfer_base_radius_km', 8, 2)->nullable()->after('transfer_service_radius_km');
            $table->decimal('transfer_base_price', 12, 2)->nullable()->after('transfer_base_radius_km');
            $table->decimal('transfer_price_per_km', 12, 2)->nullable()->after('transfer_base_price');
        });
    }

    public function down(): void
    {
        Schema::table('bravo_cars', function (Blueprint $table) {
            $table->dropColumn([
                'transfer_service_address',
                'transfer_service_lat',
                'transfer_service_lng',
                'transfer_service_radius_km',
                'transfer_base_radius_km',
                'transfer_base_price',
                'transfer_price_per_km',
            ]);
        });
    }
};
