<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameWarpsRequestFormIdToProductRequestFormId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_request_form_item', function (Blueprint $table) {
            //
            $table->renameColumn("warps_request_form_id","product_request_form_id");
        });
        Schema::table('product_request_form_logs', function (Blueprint $table) {
            //
            $table->renameColumn("warps_request_form_id","product_request_form_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_request_form_id', function (Blueprint $table) {
            //
        });
    }
}
