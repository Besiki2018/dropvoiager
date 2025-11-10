<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bravo_bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bravo_bookings', 'pickup_name')) {
                $table->string('pickup_name')->nullable()->after('object_model');
            }
            if (!Schema::hasColumn('bravo_bookings', 'pickup_source')) {
                $table->string('pickup_source', 20)->nullable()->after('pickup_name');
            }
            if (!Schema::hasColumn('bravo_bookings', 'pickup_lat')) {
                $table->decimal('pickup_lat', 12, 8)->nullable()->after('pickup_source');
            }
            if (!Schema::hasColumn('bravo_bookings', 'pickup_lng')) {
                $table->decimal('pickup_lng', 12, 8)->nullable()->after('pickup_lat');
            }
            if (!Schema::hasColumn('bravo_bookings', 'dropoff_address')) {
                $table->string('dropoff_address')->nullable()->after('pickup_lng');
            }
            if (!Schema::hasColumn('bravo_bookings', 'dropoff_lat')) {
                $table->decimal('dropoff_lat', 12, 8)->nullable()->after('dropoff_address');
            }
            if (!Schema::hasColumn('bravo_bookings', 'dropoff_lng')) {
                $table->decimal('dropoff_lng', 12, 8)->nullable()->after('dropoff_lat');
            }
            if (!Schema::hasColumn('bravo_bookings', 'distance_km')) {
                $table->decimal('distance_km', 8, 2)->nullable()->after('dropoff_lng');
            }
            if (!Schema::hasColumn('bravo_bookings', 'duration_min')) {
                $table->decimal('duration_min', 8, 2)->nullable()->after('distance_km');
            }
            if (!Schema::hasColumn('bravo_bookings', 'pricing_mode')) {
                $table->enum('pricing_mode', ['fixed', 'per_km'])->nullable()->after('duration_min');
            }
            if (!Schema::hasColumn('bravo_bookings', 'unit_price')) {
                $table->decimal('unit_price', 12, 2)->nullable()->after('pricing_mode');
            }
            if (!Schema::hasColumn('bravo_bookings', 'total_price')) {
                $table->decimal('total_price', 12, 2)->nullable()->after('unit_price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bravo_bookings', function (Blueprint $table) {
            foreach ([
                'total_price',
                'unit_price',
                'pricing_mode',
                'duration_min',
                'distance_km',
                'dropoff_lng',
                'dropoff_lat',
                'dropoff_address',
                'pickup_lng',
                'pickup_lat',
                'pickup_source',
                'pickup_name',
            ] as $column) {
                if (Schema::hasColumn('bravo_bookings', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
