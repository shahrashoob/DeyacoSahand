<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewBilOfMaterialIdToNewBillOfMaterialDegree extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('new_bill_of_material_degree', function (Blueprint $table) {
            //
            $table->foreignId("new_bill_of_material_id")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('new_bill_of_material_degree', function (Blueprint $table) {
            //
        });
    }
}
