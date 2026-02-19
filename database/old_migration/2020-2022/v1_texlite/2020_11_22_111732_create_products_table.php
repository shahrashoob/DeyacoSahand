<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string("caption");
            $table->string("code");

            $table->foreignId("unit_id")->constrained();

            $table->foreignId("warehouse_id")->constrained()->nullable();

            $table->foreignId("product_type_id")->nullable()->comment(" گروه بندی محصولات");

            $table->foreignId("goods_type_id")->nullable()->comment(" نوع کالا : محصول | ماده اولیه  ");

            $table->foreignId("active_status_id")->references("id")->on("status")->constrained()->default(1100)->comment("active/deactive");

            $table->foreignId("supply_type_id")->comment("1/ 2 => خرید/تولید  ");

            $table->integer("percent_of_waste")->default(0)->comment("در صد ضایعات");

            $table->integer("min_production")->default(0)->comment("حداقل تولید ");

            $table->integer("max_production")->default(0)->comment(" حداکثر تولید ");

            $table->integer("batch")->default(1)->comment(" بچ تولید ");

            $table->integer("min_inventory")->default(0)->comment(" حداقل موجودی ");

            $table->integer("extra_production")->default(0)->comment(" اضافه تولید | x در تخته  ");

            $table->integer("number_in_carton")->default(1)->comment("تعداد در کفی");

            $table->double("weight",15,5)->defualt(1)->comment("وزن محصول");

            $table->string("ic")->nullable()->comment("IC-Raw Material");
            $table->string("error")->nullable();
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
        Schema::dropIfExists('products');
    }
}
