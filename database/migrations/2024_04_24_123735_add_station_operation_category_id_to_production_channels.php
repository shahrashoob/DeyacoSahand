<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('production_channels', function (Blueprint $table) {
            //
            $table->foreignId("station_operation_category_id")->nullable()->
            after("production_channel_type_id")->comment("دسته عملیات: ماشین به ازای هر دسته عملیات کانال تولید های موازی دارد.");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('production_channels', function (Blueprint $table) {
            //
        });
    }
};
