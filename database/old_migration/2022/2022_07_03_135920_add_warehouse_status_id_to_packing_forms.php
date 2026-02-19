<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWarehouseStatusIdToPackingForms extends Migration
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
            $table->foreignId("warehouse_status_id")->default(4202)->comment("وضعیت وجود داشتن بسته در انبار");
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
