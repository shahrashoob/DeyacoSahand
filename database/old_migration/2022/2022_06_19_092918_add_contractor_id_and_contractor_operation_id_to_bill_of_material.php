<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContractorIdAndContractorOperationIdToBillOfMaterial extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bill_of_material_item', function (Blueprint $table) {
            //
            $table->foreignId("contractor_id")->nullable()->comment("پیمانکار");
            $table->foreignId("contractor_operation_id")->nullable()->comment("عملیات پیمانکار");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bill_of_material', function (Blueprint $table) {
            //
        });
    }
}
