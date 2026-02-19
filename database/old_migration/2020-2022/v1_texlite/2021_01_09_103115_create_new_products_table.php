<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_products', function (Blueprint $table) {
            $table->id();
            $table->string("caption");
            $table->string("code");
            $table->integer("product_id")->default(0);

            $table->integer("unit_id");

            $table->integer("warehouse_id");

            $table->integer("percent_of_waste")->default(0)->comment("در صد ضایعات");

            $table->integer("min_production")->default(0)->comment("حداقل تولید ");
        
            $table->integer("max_production")->default(0)->comment(" حداکثر تولید ");
                
            $table->integer("batch")->default(1)->comment(" بچ تولید ");
        
            $table->integer("min_inventory")->default(0)->comment(" حداقل موجودی ");
            
            $table->integer("extra_production")->default(0)->comment(" اضافه تولید | x در تخته  ");
           
            $table->integer("product_type_id")->nullable()->comment(" گروه بندی محصولات");

            $table->integer("goods_type_id")->nullable()->comment(" نوع کالا : محصول | ماده اولیه  ");

            $table->integer("active_status_id")->default(1100)->comment("active/deactive");
            $table->integer("supply_type_id")->comment("1/ 2 => خرید/تولید  ");

            $table->integer("number_in_carton")->default(1)->comment("تعداد در کفی");
            
            $table->float("weight")->defualt(1)->comment("وزن محصول");

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
        Schema::dropIfExists('new_products');
    }
}
