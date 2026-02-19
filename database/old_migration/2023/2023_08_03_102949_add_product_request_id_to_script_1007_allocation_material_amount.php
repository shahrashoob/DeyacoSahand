<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductRequestIdToScript1007AllocationMaterialAmount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('script_1007_allocation_material_amount', function (Blueprint $table) {
            //
            $table->foreignId("product_request_form_id")->nullable()->after("id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('script_1007_allocation_material_amount', function (Blueprint $table) {
            //
        });
    }
}
