<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductionFormStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('production_form_status', function (Blueprint $table) {
            $table->id();
            $table->foreignId("goods_kind_id")->references('id')->on('goods_kinds')->comment("جنس کالا");
            $table->foreignId("status_id")->references('id')->on('status')->comment("همه وضعیت های مربوط به فرم تولید");
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
        Schema::dropIfExists('production_form_status');
    }
}
