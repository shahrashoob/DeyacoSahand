<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('product_request_permissions', function (Blueprint $table) {
            //
            $table->foreignId("delivery_point_type_id")->index()->nullable()->comment("محل تحویل بار");
            $table->integer("shipping_cost")->index()->nullable()->comment("هزینه ارسال بار");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_request_permissions', function (Blueprint $table) {
            //
        });
    }
};
