<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDistancePricingToCars extends Migration
{
    public function up()
    {
        $table = 'bravo_cars';
        Schema::table($table, function (Blueprint $table) {
            if (!Schema::hasColumn('bravo_cars', 'enable_price_by_distance')) {
                $table->boolean('enable_price_by_distance')->default(false)->after('sale_price');
            }
            if (!Schema::hasColumn('bravo_cars', 'price_per_km')) {
                $table->decimal('price_per_km', 12, 2)->default(0)->after('enable_price_by_distance');
            }
        });
    }

    public function down()
    {
        Schema::table('bravo_cars', function (Blueprint $table) {
            if (Schema::hasColumn('bravo_cars', 'price_per_km')) {
                $table->dropColumn('price_per_km');
            }
            if (Schema::hasColumn('bravo_cars', 'enable_price_by_distance')) {
                $table->dropColumn('enable_price_by_distance');
            }
        });
    }
}
