<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewLotNumbers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_lot_numbers', function (Blueprint $table) {
            $table->id();
            $table->string("product_id");
            $table->string("product_code");
            $table->string("code");
            $table->string("nosa_code");
            $table->boolean("allow_delete")->default(true);
            $table->string("error");
            $table->string("warning");
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
        Schema::dropIfExists('new_lot_numbers');
    }
}
