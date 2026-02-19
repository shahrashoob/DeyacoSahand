<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillOfMaterialReplaceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bill_of_material_replace', function (Blueprint $table) {
            $table->id();
            $table->foreignId("bill_of_material_item_id");
            $table->foreignId("product_id");
            $table->foreignId("material_id");
            $table->foreignId("replace_product_id");
            $table->timestamps();
        });

        DB::statement("ALTER TABLE `bill_of_material_replace` comment 'برای هر ردیف BOM می توان یک یا چند کالای جایگزین مصرف انتخاب نمود'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bill_of_material_replace');
    }
}
