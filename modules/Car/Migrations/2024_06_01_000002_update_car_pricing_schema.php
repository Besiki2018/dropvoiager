<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bravo_cars', function (Blueprint $table) {
            if (!Schema::hasColumn('bravo_cars', 'pricing_mode')) {
                $table->enum('pricing_mode', ['fixed', 'per_km'])->default('per_km')->after('price');
            }
            if (!Schema::hasColumn('bravo_cars', 'fixed_price')) {
                $table->decimal('fixed_price', 12, 2)->nullable()->after('pricing_mode');
            }
            if (!Schema::hasColumn('bravo_cars', 'price_per_km')) {
                $table->decimal('price_per_km', 12, 2)->nullable()->after('fixed_price');
            }
        });

        if (Schema::hasColumn('bravo_cars', 'service_radius_km')) {
            DB::statement("ALTER TABLE bravo_cars MODIFY service_radius_km DECIMAL(8,2) NOT NULL DEFAULT 2.00");
        }

        foreach (['base_radius_km', 'base_price', 'price_per_km_outside'] as $column) {
            if (Schema::hasColumn('bravo_cars', $column)) {
                Schema::table('bravo_cars', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }

    public function down(): void
    {
        Schema::table('bravo_cars', function (Blueprint $table) {
            if (Schema::hasColumn('bravo_cars', 'price_per_km')) {
                $table->dropColumn('price_per_km');
            }
            if (Schema::hasColumn('bravo_cars', 'fixed_price')) {
                $table->dropColumn('fixed_price');
            }
            if (Schema::hasColumn('bravo_cars', 'pricing_mode')) {
                $table->dropColumn('pricing_mode');
            }
        });

        Schema::table('bravo_cars', function (Blueprint $table) {
            if (!Schema::hasColumn('bravo_cars', 'base_radius_km')) {
                $table->decimal('base_radius_km', 8, 2)->default(0)->after('service_radius_km');
            }
            if (!Schema::hasColumn('bravo_cars', 'base_price')) {
                $table->decimal('base_price', 12, 2)->default(0)->after('base_radius_km');
            }
            if (!Schema::hasColumn('bravo_cars', 'price_per_km_outside')) {
                $table->decimal('price_per_km_outside', 12, 2)->default(0)->after('base_price');
            }
        });

        if (Schema::hasColumn('bravo_cars', 'service_radius_km')) {
            DB::statement("ALTER TABLE bravo_cars MODIFY service_radius_km DECIMAL(8,2) NOT NULL DEFAULT 0.00");
        }
    }
};
