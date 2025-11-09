<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('car_pickup_locations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('car_id');
            $table->string('name');
            $table->string('address')->nullable();
            $table->decimal('lat', 12, 8);
            $table->decimal('lng', 12, 8);
            $table->decimal('base_price', 12, 2)->nullable();
            $table->decimal('price_coefficient', 8, 4)->default(1);
            $table->timestamps();

            $table->foreign('car_id')->references('id')->on('bravo_cars')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('car_pickup_locations');
    }
};
