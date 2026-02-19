<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexToProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            //
            $table->index("unit_id");
            $table->index("sub_unit_id");
            $table->index("sub_unit2_id");
            $table->index("goods_type_id");
            $table->index("active_status_id");
            $table->index("supply_type_id");
            $table->index("goods_kind_id");
            $table->index("image_id");
            $table->index("product_service_type_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
}
