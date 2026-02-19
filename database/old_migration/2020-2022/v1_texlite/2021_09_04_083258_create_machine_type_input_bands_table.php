<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachineTypeInputBandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // باند های ووردی نوع ماشین
        Schema::create('machine_type_input_bands', function (Blueprint $table) {
            $table->id();
            $table->foreignId("machine_type_id");
            $table->integer("code");
            $table->string("caption");
            $table->integer("input_line_number")->default(1)->comment("تعداد خط ورودی هر باند ورودی");
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
        Schema::dropIfExists('machine_type_input_bands');
    }
}
