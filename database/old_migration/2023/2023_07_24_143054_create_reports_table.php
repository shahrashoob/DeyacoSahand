<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string("caption");
            $table->string("code");
            $table->integer("show_in_real_time_dashboard")->comment("آیا گزارش در داشبورد لحظه ای وجود دارد.");
            $table->integer("show_in_cross_sectional_dashboard")->comment("آیا گزارش در داشبورد مقطعی ای وجود دارد.");
            $table->string("directory_namespace");
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
        Schema::dropIfExists('reports');
    }
}
