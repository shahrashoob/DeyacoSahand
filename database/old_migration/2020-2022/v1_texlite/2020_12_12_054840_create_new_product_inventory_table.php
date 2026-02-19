<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewProductInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_product_inventory', function (Blueprint $table) {
            $table->id();
            $table->integer("product_id");
            $table->string("product_code");
            $table->string("caption");
            $table->string("error")->nullable();
            $table->double('end_inventory', 15, 2)->default(0);
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
        Schema::dropIfExists('new_product_inventory');
    }
}
