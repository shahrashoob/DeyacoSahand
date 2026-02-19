<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId("product_id");
            $table->foreignId("goods_kind_id");
            $table->string("caption");
            $table->string("code");

            $table->foreignId("unit_id");
            $table->foreignId("sub_unit_id")->nullable();
            $table->foreignId("sub_unit2_id")->nullable();

            $table->double("weight", 15, 5)->comment("وزن محصول");
            $table->foreignId("frame_ratio_unit2")->nullable()->comment("نسبت واحد اصلی به واحد فرعی 2");

            $table->longText("property_json")->comment("json مشخصات کالا");
            $table->longText("consume_json")->comment("json کالای مصرفی");
            $table->foreignId("bill_of_material_log_id")->comment("نسخه BOM");

            $table->integer("change_product_cols")->comment("آیا مقدار ستون های اصلی کالا تغییر کرده");
            $table->integer("change_property")->comment("آیا مقدار مشخصات کالا تغییر کرده");
            $table->integer("change_consume")->comment("آیا کالای مصرفی تغییر کرده");
            $table->integer("change_bom")->comment("آیا BOM تغییر کرده");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
