<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductionFormItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('production_form_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId("production_form_id");
            $table->foreignId("production_id");
            $table->foreignId("product_id");
            $table->foreignId("lot_number_id");
            $table->float("amount")->default(0);
            $table->float("sub_amount")->default(0);
            $table->float("amount_after_control")->default(0)->comment("مقدار پس از کنترل");
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
        Schema::dropIfExists('production_form_item');
    }
}
