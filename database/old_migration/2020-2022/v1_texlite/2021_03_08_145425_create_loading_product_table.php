<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoadingProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loading_product', function (Blueprint $table) {
            $table->id();
            $table->integer("order_id");
            $table->integer("loading_process_id");
            $table->integer("product_id");
            $table->integer("carton_collect")->nullable();
            $table->integer("carton_load")->nullable();
            $table->integer("status_id");
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
        Schema::dropIfExists('loading_product');
    }
}
