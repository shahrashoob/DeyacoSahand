<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachineAllocationMaterialConsumedItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('machine_allocation_material_consumed_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId( "machine_allocation_material_consumed_id" );
            $table->foreignId( "allocation_id" );
            $table->foreignId( "production_id" );
            $table->foreignId( "product_id" );
            $table->foreignId( "material_id" );
            $table->foreignId( "lot_number_id" );
            $table->foreignId( "packing_form_item_id" );
            $table->double( "amount_consumed",10,5 )->comment("مقدار مصرف مواد اولیه");
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
        Schema::dropIfExists('machine_allocation_material_consumed_item');
    }
}
