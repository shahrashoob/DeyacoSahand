<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductionDateTimesTable extends Migration
{
    /**
     * Run the migrations.
     * زمان های شروع و پایان هر شیفت را مشخص می کند
     * @return void
     */
    public function up()
    {
        Schema::create('production_date_times', function (Blueprint $table) {
            
            
            $table->id();

            
            $table->integer("production_card_id");
            $table->dateTime("start_datetime")->nullable();
            $table->dateTime("end_datetime")->nullable();
            $table->float("sub_productivity_index")->nullable()->comment("شاخص عملکرد خرد ");
            $table->float("shift_time")->nullable()->comment(" زمان شیفت - دقیقه ");
            $table->integer("version")->default(1);

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
        Schema::dropIfExists('production_date_times');
    }
}
