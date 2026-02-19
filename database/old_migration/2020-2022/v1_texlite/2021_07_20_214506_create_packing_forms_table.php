<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackingFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packing_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId("product_id");
            $table->foreignId("lot_number_id");
            $table->foreignId("carrier_id");
            $table->foreignId("form_id");
            $table->foreignId("status_id");
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
        Schema::dropIfExists('packing_forms');
    }
}
