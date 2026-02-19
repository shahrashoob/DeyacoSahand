<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachineAllocationModificationPackingFormTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('machine_allocation_modification_packing_form', function (Blueprint $table) {
            $table->id();
            $table->foreignId("machine_allocation_modification_id");
            $table->foreignId("product_id")->comment(" مواد اولیه برگشت شده");
            $table->foreignId("packing_form_id")->comment(" بسته بندی برگشت شده");
            $table->foreignId("sub_packing_form_number")->comment("تعداد بسته بندی های فرعی");
            $table->double("gross_weight",15,7)->comment(" وزن ناخالص برگشت شده");
            $table->double("weight",15,7)->comment("وزن مواد اولیه");
            $table->double("amount",15,7)->comment("مقدار اصلی مواد اولیه");
            $table->timestamps();
        });

        DB::statement("ALTER TABLE `machine_allocation_modification_packing_form` comment 'در زمان ثبت برگشت مواد اولیه، اطلاعات بسته بندی های مواد اولیه برگشت شده در این جدول ذخیره می گردد'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('machine_allocation_modification_packing_form');
    }
}
