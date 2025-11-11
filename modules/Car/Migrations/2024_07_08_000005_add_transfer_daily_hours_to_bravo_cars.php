<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTransferDailyHoursToBravoCars extends Migration
{
    public function up()
    {
        Schema::table('bravo_cars', function (Blueprint $table) {
            if (!Schema::hasColumn('bravo_cars', 'transfer_time_start')) {
                $table->time('transfer_time_start')->nullable()->after('price_per_km');
            }
            if (!Schema::hasColumn('bravo_cars', 'transfer_time_end')) {
                $table->time('transfer_time_end')->nullable()->after('transfer_time_start');
            }
        });
    }

    public function down()
    {
        Schema::table('bravo_cars', function (Blueprint $table) {
            if (Schema::hasColumn('bravo_cars', 'transfer_time_start')) {
                $table->dropColumn('transfer_time_start');
            }
            if (Schema::hasColumn('bravo_cars', 'transfer_time_end')) {
                $table->dropColumn('transfer_time_end');
            }
        });
    }
}
