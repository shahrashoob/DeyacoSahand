<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWarehouseIdToPackingForms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packing_forms', function (Blueprint $table) {
            //
            $table->foreignId("warehouse_id")->nullable()->after("warehouse_status_id")->comment("در حال حاضر بسته در کدام انبار قرار دارد");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('packing_forms', function (Blueprint $table) {
            //
        });
    }
}
