<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachineTypeMachineFaultProductFaultTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('machine_type_machine_fault_product_fault', function (Blueprint $table) {
            $table->id();
            $table->foreignId("machine_type_id");
            $table->foreignId("machine_fault_id");
            $table->foreignId("machine_type_machine_fault_id");
            $table->foreignId("product_fault_id");
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
        Schema::dropIfExists('machine_type_machine_fault_product_fault');
    }
}
