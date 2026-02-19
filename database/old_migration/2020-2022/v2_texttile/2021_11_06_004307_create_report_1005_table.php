<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReport1005Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_1005', function (Blueprint $table) {
            $table->id();
            $table->foreignId("owner_user_id");
            $table->foreignId("machine_id");
            $table->foreignId("user_id");
            $table->foreignId("production_status_id");
            $table->foreignId("on_status_id");
            $table->foreignId("active_status_id");
            $table->foreignId("maintenance_status_id");
            $table->foreignId("machine_event_type_id");
            $table->foreignId("machine_off_reason_id");
            $table->bigInteger("contour_sum_value");
            $table->bigInteger("time_in_second");
            $table->timestamps();
            $table->dateTime("end_date")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('report_1005');
    }
}
