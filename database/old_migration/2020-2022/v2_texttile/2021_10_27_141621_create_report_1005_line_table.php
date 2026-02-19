<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReport1005LineTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_1005_line', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id");
            $table->foreignId("line_id");
            $table->foreignId("station_id");
            $table->foreignId("machine_type_id");
            $table->foreignId("machine_id");
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
        Schema::dropIfExists('report_1005_line');
    }
}
