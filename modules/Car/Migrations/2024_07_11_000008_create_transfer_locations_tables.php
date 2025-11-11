<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('car_transfer_locations')) {
            Schema::create('car_transfer_locations', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('vendor_id')->nullable()->index();
                $table->string('name');
                $table->string('address')->nullable();
                $table->decimal('lat', 12, 8)->nullable();
                $table->decimal('lng', 12, 8)->nullable();
                $table->unsignedTinyInteger('map_zoom')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('car_transfer_service_centers')) {
            Schema::create('car_transfer_service_centers', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('location_id')->nullable()->index();
                $table->unsignedBigInteger('vendor_id')->nullable()->index();
                $table->string('name');
                $table->string('address')->nullable();
                $table->decimal('lat', 12, 8)->nullable();
                $table->decimal('lng', 12, 8)->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('car_transfer_service_centers');
        Schema::dropIfExists('car_transfer_locations');
    }
};
