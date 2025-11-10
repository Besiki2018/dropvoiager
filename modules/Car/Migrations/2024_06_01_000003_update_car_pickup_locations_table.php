<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('car_pickup_locations', function (Blueprint $table) {
            if (!Schema::hasColumn('car_pickup_locations', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('lng');
            }
        });

        foreach (['address', 'base_price', 'price_coefficient'] as $column) {
            if (Schema::hasColumn('car_pickup_locations', $column)) {
                Schema::table('car_pickup_locations', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }

    public function down(): void
    {
        Schema::table('car_pickup_locations', function (Blueprint $table) {
            if (!Schema::hasColumn('car_pickup_locations', 'address')) {
                $table->string('address')->nullable()->after('name');
            }
            if (!Schema::hasColumn('car_pickup_locations', 'base_price')) {
                $table->decimal('base_price', 12, 2)->nullable()->after('lng');
            }
            if (!Schema::hasColumn('car_pickup_locations', 'price_coefficient')) {
                $table->decimal('price_coefficient', 8, 4)->default(1)->after('base_price');
            }
            if (Schema::hasColumn('car_pickup_locations', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
};
