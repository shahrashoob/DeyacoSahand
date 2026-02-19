<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImportProductPropertyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('import_product_property', function (Blueprint $table) {
            $table->id();
            $table->integer("row_id");
            $table->integer("col_id");
            $table->string("goods_kind_id");
            $table->string("product_code");
            $table->string("product_id");
            $table->string("property_id");
            $table->string("value");
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
        Schema::dropIfExists('import_product_property');
    }
}
