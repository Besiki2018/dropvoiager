<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('car_pickup_locations', function (Blueprint $table) {
            $addedVendorId = false;
            if (!Schema::hasColumn('car_pickup_locations', 'vendor_id')) {
                $table->unsignedBigInteger('vendor_id')->nullable()->after('car_id');
                $addedVendorId = true;
            }
            if (!Schema::hasColumn('car_pickup_locations', 'address')) {
                $table->string('address')->nullable()->after('name');
            }
            if (!Schema::hasColumn('car_pickup_locations', 'map_zoom')) {
                $table->unsignedTinyInteger('map_zoom')->nullable()->after('lng');
            }
            if (!Schema::hasColumn('car_pickup_locations', 'service_center_name')) {
                $table->string('service_center_name')->nullable()->after('map_zoom');
            }
            if (!Schema::hasColumn('car_pickup_locations', 'service_center_address')) {
                $table->string('service_center_address')->nullable()->after('service_center_name');
            }
            if (!Schema::hasColumn('car_pickup_locations', 'service_center_lat')) {
                $table->decimal('service_center_lat', 12, 8)->nullable()->after('service_center_address');
            }
            if (!Schema::hasColumn('car_pickup_locations', 'service_center_lng')) {
                $table->decimal('service_center_lng', 12, 8)->nullable()->after('service_center_lat');
            }

            if ($addedVendorId) {
                $table->index('vendor_id', 'car_pickup_locations_vendor_id_index');
            }
        });
    }

    public function down(): void
    {
        Schema::table('car_pickup_locations', function (Blueprint $table) {
            if (Schema::hasColumn('car_pickup_locations', 'service_center_lng')) {
                $table->dropColumn('service_center_lng');
            }
            if (Schema::hasColumn('car_pickup_locations', 'service_center_lat')) {
                $table->dropColumn('service_center_lat');
            }
            if (Schema::hasColumn('car_pickup_locations', 'service_center_address')) {
                $table->dropColumn('service_center_address');
            }
            if (Schema::hasColumn('car_pickup_locations', 'service_center_name')) {
                $table->dropColumn('service_center_name');
            }
            if (Schema::hasColumn('car_pickup_locations', 'map_zoom')) {
                $table->dropColumn('map_zoom');
            }
            if (Schema::hasColumn('car_pickup_locations', 'address')) {
                $table->dropColumn('address');
            }
            if (Schema::hasColumn('car_pickup_locations', 'vendor_id')) {
                try {
                    $table->dropIndex('car_pickup_locations_vendor_id_index');
                } catch (\Throwable $exception) {
                    // Index might not exist yet – ignore so the column can still be dropped.
                }
                $table->dropColumn('vendor_id');
            }
        });
    }
};
