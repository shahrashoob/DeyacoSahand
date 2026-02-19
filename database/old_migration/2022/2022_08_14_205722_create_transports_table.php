<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transports', function (Blueprint $table) {
            $table->id();
            $table->integer("series");
            $table->string("order_code");
            $table->foreignId("order_id");
            $table->foreignId("user_id");
            $table->foreignId("status_id");
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `transports` comment 'کلاس موقت ویژه دوره پیاده سازی جهت ثبت اطلاعات عدل بندی'");


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transports');
    }
}
