<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAllocationDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('allocation_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId("allocation_id");
            $table->foreignId("machine_allocation_id");
            $table->foreignId("allocation_data_type_id");
            $table->float("float_value")->nullable();
            $table->float("string_value")->nullable();
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
        Schema::dropIfExists('allocation_datas');
    }
}
