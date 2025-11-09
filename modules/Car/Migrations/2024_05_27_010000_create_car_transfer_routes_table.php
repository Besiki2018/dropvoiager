<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('car_transfer_routes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->string('pickup_name');
            $table->string('pickup_address')->nullable();
            $table->decimal('pickup_lat', 12, 8)->nullable();
            $table->decimal('pickup_lng', 12, 8)->nullable();
            $table->string('dropoff_name');
            $table->string('dropoff_address')->nullable();
            $table->decimal('dropoff_lat', 12, 8)->nullable();
            $table->decimal('dropoff_lng', 12, 8)->nullable();
            $table->string('status')->default('publish');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('car_transfer_routes');
    }
};
