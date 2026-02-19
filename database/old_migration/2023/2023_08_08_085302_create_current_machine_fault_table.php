<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCurrentMachineFaultTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('current_machine_faults', function (Blueprint $table) {
            $table->id();
            $table->foreignId("machine_id");
            $table->foreignId("user_id");
            $table->foreignId("machine_fault_id");
            $table->foreignId("active_status_id")->comment("وضعیت فعال / غیر فعال(1100)");
            $table->foreignId("status_id")->comment("وضعیت نقص در ماشین (6004)");
            $table->foreignId("maintenance_id");
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
        Schema::dropIfExists('current_machine_fault');
    }
}
