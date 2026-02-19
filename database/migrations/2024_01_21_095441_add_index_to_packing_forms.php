<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexToPackingForms extends Migration
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
            $table->index("carrier_id");
            $table->index("form_id");
            $table->index("status_id");
            $table->index("packing_type_id");
            $table->index("packing_form_parent_id");
            $table->index("warehouse_status_id");
            $table->index("warehouse_id");
            $table->index("packing_form_master_id");
            $table->index("reality_type_id");
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
