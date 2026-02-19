<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForecastAmountToProductionForms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('production_form_item', function (Blueprint $table) {
            //
            $table->double("forecast_amount",8,2)->default(0)->after("product_id")->comment("مقدار پیش بینی شده برای تولید");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('production_forms', function (Blueprint $table) {
            //
        });
    }
}
