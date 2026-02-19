<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReplaceProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('replace_products', function (Blueprint $table) {
            $table->id();
            $table->integer("product_id");
            $table->integer("replace_product_id")->comment("محصول جایگزین");
            $table->integer("ratio")->default(1)->comment("ضریب جایگزین");
            $table->integer("replace_type_id")->comment("نوع جایگزینی: مصرف، تولید");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('replace_products');
    }
}
