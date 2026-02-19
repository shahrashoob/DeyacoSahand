<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSuplyTypeIdToProductRoute extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_routes', function (Blueprint $table) {
            //
            $table->foreignId("supply_type_id")->default(1)->comment("با توجه به نوع تامین، نوع مسیر محصول مشخص می شود.");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_route', function (Blueprint $table) {
            //
        });
    }
}
