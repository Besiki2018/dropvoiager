<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $afterColumn = Schema::hasColumn('car_pickup_locations', 'address')
            ? 'address'
            : 'name';

        Schema::table('car_pickup_locations', function (Blueprint $table) use ($afterColumn) {
            if (!Schema::hasColumn('car_pickup_locations', 'place_id')) {
                $table->string('place_id')->nullable()->after($afterColumn);
            }
        });
    }

    public function down(): void
    {
        Schema::table('car_pickup_locations', function (Blueprint $table) {
            if (Schema::hasColumn('car_pickup_locations', 'place_id')) {
                $table->dropColumn('place_id');
            }
        });
    }
};
