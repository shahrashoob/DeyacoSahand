<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveColumnsFromWarpsRequestForms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('warps_request_forms', function (Blueprint $table) {
            //
            $table->dropColumn("production_id");
            $table->dropColumn("product_id");
            $table->dropColumn("warehouse_product_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('warps_request_forms', function (Blueprint $table) {
            //
        });
    }
}
