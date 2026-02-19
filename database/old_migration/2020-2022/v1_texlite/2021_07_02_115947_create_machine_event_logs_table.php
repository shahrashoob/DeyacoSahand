<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachineEventLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('machine_event_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId("machine_id");
            $table->foreignId("machine_event_type_id")->nullable();
            $table->float("contour_1_value")->nullable()->comment("مقدار شمارنده 1");
            $table->float("contour_2_value")->nullable()->comment("مقدار شمارنده 2");
            $table->float("contour_3_value")->nullable()->comment("مقدار شمارنده 3");
            $table->float("contour_4_value")->nullable()->comment("مقدار شمارنده 4");
            $table->float("contour_5_value")->nullable()->comment("مقدار شمارنده 5");
            $table->foreignId("shift_work_id")->nullable()->comment("شیف کاری");
            $table->foreignId("user_id");

            $table->datetime("created_at")->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->datetime("updated_at")->default(DB::raw('CURRENT_TIMESTAMP'));

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('machine_event_logs');
    }
}
