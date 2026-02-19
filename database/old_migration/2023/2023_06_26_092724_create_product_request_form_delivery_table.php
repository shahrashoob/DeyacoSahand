<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductRequestFormDeliveryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_request_form_delivery', function (Blueprint $table) {
            $table->id();
            $table->foreignId("product_request_form_id");
//            $table->foreignId("product_request_form_item_id");
            $table->foreignId("packing_form_id")->comment("بسته بندی که قرار است، تراکنش روی آن انجام شود.");
//            $table->foreignId("master_packing_form_id")->comment("بسته سطح بالا، بسته ای که قرار است، به کاربر نمایش داده شود");
            $table->foreignId("packing_type_id")->comment("نوع بسته بندی");
//            $table->foreignId("degree_id")->comment("نوع بسته بندی");
            $table->foreignId("carrier_id")->nullable();
//            $table->foreignId("lot_number_id")->nullable();
            $table->foreignId("unit_id")->nullable();
            $table->integer("transport_item_id")->nullable()->comment("شماره بسته بندی حمل و نقل");

            $table->double("final_amount",15,6)->comment("مقدار نهایی بسته بندی نمایش داده شده به کاربر");
            $table->integer("sub_packing_form_number")->comment("تعداد بسته بندی های فرعی بسته نمایشی");
            $table->integer("sub_packing_form_number_selected")->comment("تعداد بسته بندی های فرعی انتخاب شده");
            $table->integer("count_product_id")->comment("تنوع آیتم در بسته نمایشی");
            $table->integer("count_item")->comment("تعداد آیتم");
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
        Schema::dropIfExists('product_request_form_delivery');
    }
}
