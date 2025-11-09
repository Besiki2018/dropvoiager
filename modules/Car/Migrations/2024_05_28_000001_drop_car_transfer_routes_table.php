<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('car_transfer_routes')) {
            Schema::drop('car_transfer_routes');
        }
    }

    public function down(): void
    {
        // The original table definition is no longer available, so we only recreate an empty shell for rollbacks.
        Schema::create('car_transfer_routes', function ($table) {
            $table->bigIncrements('id');
            $table->timestamps();
        });
    }
};
