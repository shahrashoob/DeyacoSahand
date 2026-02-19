<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeSomeColFromProductRequestFormItem extends Migration
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
            $table->integer("band_code")->nullable()->change();
            $table->integer("input_line_code")->nullable()->change();
            $table->foreignId("current_machine_input_output_band_id")->nullable()->change();
            $table->foreignId("warehouse_product_id")->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_request_form_item', function (Blueprint $table) {
            //
        });
    }
}
