<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexToPackingFormItem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packing_form_item', function (Blueprint $table) {
            //
            $table->index("packing_form_id");
            $table->index("production_form_item_id");
            $table->index("production_form_item_lot_number_id");
            $table->index("status_id");
            $table->index("lot_number_id");
            $table->index("degree_id");
            $table->index("machine_allocation_actual_cost_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('packing_form_item', function (Blueprint $table) {
            //
        });
    }
}
