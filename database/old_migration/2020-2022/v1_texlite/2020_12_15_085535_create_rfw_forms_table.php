<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRfwFormsTable extends Migration
{
    /**
     * Run the migrations.
     * فرم درخواست کالا از انبار
     * @return void
     */
    public function up()
    {
        Schema::create('rfw_forms', function (Blueprint $table) {
            $table->id();
            $table->string("code")->nullable();
            $table->integer("order_id");
            $table->integer("order_list_id");
            $table->integer("production_card_id");
            $table->integer("user_id");

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
        Schema::dropIfExists('rfw_forms');
    }
}
