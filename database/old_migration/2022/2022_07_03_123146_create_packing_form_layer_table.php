<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackingFormLayerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packing_form_layer', function (Blueprint $table) {
            $table->id();

            $table->foreignId("packing_form_id")->nullable()->comment("اگر بسته بندی چند لایه باشد و بر روی باند حامل یک بسته بندی قرار داشته باشد، شناسه بسته بندی در اینجا درج می شود.");
            $table->foreignId("packing_form_item_id")->nullable()->comment("شناسه تکه های بسته بندی، در صورتی که packing_form_id مقدار داشته باشد، این فیلد نال است.");

            $table->foreignId("carrier_id")->nullable();
            $table->integer("band_code")->comment("باند حامل");
            $table->integer("layer_code");
            $table->timestamps();
        });

        DB::statement("ALTER TABLE `packing_form_layer` comment 'ذخیره اطلاعات لایه های بسته بندی'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('packing_form_layer');
    }
}
