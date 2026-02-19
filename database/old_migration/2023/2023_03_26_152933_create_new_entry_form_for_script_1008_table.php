<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewEntryFormForScript1008Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_entry_form_for_script_1008', function (Blueprint $table) {
            $table->id();
            $table->foreignId("product_id");
            $table->foreignId("packing_type_id");
            $table->foreignId("degree_id");
            $table->foreignId("warehouse_id");
            $table->foreignId("trans_kind_id");
            $table->foreignId("opp_kind_id");
            $table->foreignId("cost_center_id");
            $table->foreignId("user_id");
            $table->string("lot_number_id");
            $table->text("description");
            $table->longText("packing_form_rows");
            $table->string("print");
            $table->foreignId("status_id")->default(410100);
            $table->foreignId("form_id")->nullable();
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
        Schema::dropIfExists('new_entry_form_for_script_1008');
    }
}
