<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImportProductActualCostTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('import_product_actual_cost', function (Blueprint $table) {
            $table->id();
            $table->foreignId("product_id")->nullable();
            $table->foreignId("packing_type_id")->nullable();
            $table->foreignId("status_id")->nullable();


            $table->string("product_code")->nullable();
            $table->string("packing_type_code")->nullable();

            $table->string("product_caption")->nullable();
            $table->string("packing_type_caption")->nullable();

            $table->text("error")->nullable();

            $table->double("price", 15, 2)->comment("قیمت بروز (ریال)");
            $table->timestamps();;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('import_product_actaul_cost');
    }
}
