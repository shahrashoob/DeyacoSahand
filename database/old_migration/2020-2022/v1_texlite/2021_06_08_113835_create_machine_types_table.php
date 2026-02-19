<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachineTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('machine_types', function (Blueprint $table) {
            $table->id(); $table->foreignId("station_id")->constrained();
            $table->string("code");
            $table->string("caption");
            $table->integer("section_number")->comment("تعداد سکشن های ماشین");
            $table->integer("position_number")->comment("تعداد چشمه های ماشین");
            $table->integer("ic")->comment("مرکز هزینه گروه ماشین");
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('machine_types');
    }
}
