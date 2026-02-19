<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDegreeIdAndWarehouseIdToProductTariffLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_tariff_logs', function (Blueprint $table) {
            //
            $table->foreignId("degree_id");
            $table->foreignId("warehouse_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_tariff_logs', function (Blueprint $table) {
            //
        });
    }
}
