<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExtraProdactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('extra_prodaction', function (Blueprint $table) {
            $table->id();
            $table->integer("product_id");
            $table->integer("amount")->default(0)->comment("تعداد اضافه تولید بر اساس کارتن");
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
        Schema::dropIfExists('extra_prodaction');
    }
}
