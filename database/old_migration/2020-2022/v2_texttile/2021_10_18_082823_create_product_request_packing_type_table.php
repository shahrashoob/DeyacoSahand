<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductRequestPackingTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_request_packing_type', function (Blueprint $table) {
            $table->id();
            $table->foreignId("product_request_form_id");
            $table->foreignId("product_request_form_item_id");
            $table->foreignId("product_id");
            $table->foreignId("packing_type_id");
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
        Schema::dropIfExists('product_request_packing_type');
    }
}
