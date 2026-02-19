<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRejectProductFormItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reject_product_form_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId("reject_product_form_id");
            $table->foreignId("packing_form_id");
            $table->integer("packing_is_safe")->comment("آیا بسته بندی سالم است");
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
        Schema::dropIfExists('reject_product_form_item');
    }
}
