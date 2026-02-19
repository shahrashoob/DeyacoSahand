<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewWarehouseHandlingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_warehouse_handling', function (Blueprint $table) {
            $table->id();
            $table->integer("warehouse_id");
            $table->string("product_code")->nullable();
            $table->integer("product_id");
            $table->integer("trans_kind");
            $table->integer("opp_kind");
            $table->text("message_text")->nullable();
            $table->double("amount",15,10)->default(0);
            $table->integer("status_id")->default(524000100);
            $table->string("error");
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
        Schema::dropIfExists('new_warehouse_handling');
    }
}
