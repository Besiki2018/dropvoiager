<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAvailableHoursToBravoCarDates extends Migration
{
    public function up()
    {
        Schema::table('bravo_car_dates', function (Blueprint $table) {
            if (!Schema::hasColumn('bravo_car_dates', 'available_hours')) {
                $table->json('available_hours')->nullable()->after('end_date');
            }
        });
    }

    public function down()
    {
        Schema::table('bravo_car_dates', function (Blueprint $table) {
            if (Schema::hasColumn('bravo_car_dates', 'available_hours')) {
                $table->dropColumn('available_hours');
            }
        });
    }
}
