<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductionWaitingStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('production_waiting_status', function (Blueprint $table) {
            $table->id();
            $table->foreignId("goods_kind_id")->references('id')->on('goods_kinds')->comment("جنس کالا");
            $table->foreignId("status_id")->references('id')->on('status')->comment("همه وضعیت های مربوط به نوع درانتظار 50010");
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
        Schema::dropIfExists('production_waiting_status');
    }
}
