<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductionFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // جدول فرم های تولید
        Schema::create('production_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId("machine_id");
            $table->foreignId("production_id");
            $table->foreignId("product_id")->comment("محصول تولید شده");
            $table->foreignId("lot_number_id");
            $table->foreignId("carrier_id");
            $table->float("amount")->comment("مقدار تولید شده");
            $table->float("sub_amount")->comment("مقدار فرعی( موقت)");
            $table->float("amount_after_control")->comment("مقدار پس از کنترل کیفیت");
            $table->foreignId("status_id");
            $table->dateTime("install_date")->comment("زمان نصب ");
            $table->dateTime("end_date")->comment("زمان استخراج ");
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
        Schema::dropIfExists('production_forms');
    }
}
