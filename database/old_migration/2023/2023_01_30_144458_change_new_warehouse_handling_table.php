<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeNewWarehouseHandlingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('new_warehouse_handling', function (Blueprint $table) {
            //
            $table->drop();
        });
        Schema::create('new_warehouse_handling', function (Blueprint $table) {
            //
            $table->id();
            $table->string("packing_form_number");
            $table->string("packing_form_id");
            $table->string("warehouse_code");
            $table->string("warehouse_id");
            $table->double("amount");
            $table->integer("opp_kind");
            $table->integer("trans_kind");
            $table->string("ic");
            $table->string("error");
            $table->string("warning");
            $table->string("has_error");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
