<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSomeCulomnsToLineProductStation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('line_product_station', function (Blueprint $table) {
            //
            $table->foreignId("contractor_id")->nullable()->comment("نام پیمانکار");
            $table->foreignId("contractor_operation_id")->nullable()->comment("عملیات پیمانکار");
            $table->integer("delivery_time")->nullable()->comment("مدت زمان تحویل کالا توسط پیمانکار");
            $table->integer("receiving_time")->nullable()->comment("مدت زمان دریافت کالا توسط پیمانکار");


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('line_product_station', function (Blueprint $table) {
            //
        });
    }
}
