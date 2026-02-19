<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachineTypeOutputBandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('machine_type_output_bands', function (Blueprint $table) {
            $table->id();
            $table->foreignId("machine_type_id");
            $table->integer("code");
            $table->string("caption");
            $table->integer("output_line_number")->default(1)->comment("تعداد خط خروجی هر باند خروجی");
            $table->foreignId("active_status_id")->comment("وضعیت فعال بودن");
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
        Schema::dropIfExists('machine_type_output_bands');
    }
}
