<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachineFaultMachineFaultSignTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('machine_fault_machine_fault_sign', function (Blueprint $table) {
            $table->id();
            $table->foreignId("machine_fault_id");
            $table->foreignId("machine_fault_sign_id");
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
        Schema::dropIfExists('machine_fault_machine_fault_sign');
    }
}
