<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSomColumntToMachineLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('machine_logs');

        Schema::create('machine_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId("machine_id");
            $table->foreignId("current_production_id")->nullable();
            $table->foreignId("reserve_production_id")->nullable();
            $table->foreignId("message_id")->nullable();
            $table->foreignId("user_id");

            $table->foreignId("active_status_id")->nullable()->comment("وضعیت فعال بودن ");
            $table->foreignId("on_status_id")->nullable()->comment("وضعیت روشن بودن");
            $table->foreignId("production_status_id")->nullable()->comment("وضعیت تولید ");
            $table->foreignId("machine_off_reason_id")->nullable()->comment("دلیل خاموشی");


            $table->foreignId("machine_event_type_id")->nullable()->comment("نوع رویداد ماشین");
            $table->float("contour_1_value")->nullable()->comment("مقدار شمارنده 1");
            $table->float("contour_2_value")->nullable()->comment("مقدار شمارنده 2");
            $table->float("contour_3_value")->nullable()->comment("مقدار شمارنده 3");
            $table->float("contour_4_value")->nullable()->comment("مقدار شمارنده 4");
            $table->float("contour_5_value")->nullable()->comment("مقدار شمارنده 5");
            $table->foreignId("shift_work_id")->nullable()->comment("شیف کاری");

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
        Schema::table('machine_logs', function (Blueprint $table) {
            //
        });
    }
}
