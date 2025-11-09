<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bravo_cars', function (Blueprint $table) {
            $table->string('service_center_address')->nullable();
            $table->decimal('service_center_lat', 12, 8)->nullable();
            $table->decimal('service_center_lng', 12, 8)->nullable();
            $table->decimal('service_radius_km', 8, 2)->default(0)->comment('Maximum service radius in kilometers');
            $table->decimal('base_radius_km', 8, 2)->default(0)->comment('Base price radius in kilometers');
            $table->decimal('base_price', 12, 2)->default(0);
            $table->decimal('price_per_km_outside', 12, 2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('bravo_cars', function (Blueprint $table) {
            $table->dropColumn([
                'service_center_address',
                'service_center_lat',
                'service_center_lng',
                'service_radius_km',
                'base_radius_km',
                'base_price',
                'price_per_km_outside',
            ]);
        });
    }
};
