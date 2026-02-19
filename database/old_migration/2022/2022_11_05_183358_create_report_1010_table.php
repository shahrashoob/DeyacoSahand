<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReport1010Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_1010', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id");
            $table->foreignId("machine_log_id");
            $table->foreignId("machine_id");
            $table->foreignId("product_id");
            $table->foreignId("operator_id");
            $table->dateTime("created_at");
            $table->integer("theory_contour")->comment("مقدار تئوری کنتور");
            $table->integer("operation_contour")->comment("مقدار عملیاتی کنتور");
            $table->integer("time_in_minute")->comment("زمان به دقیقه");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('report_1010');
    }
}
