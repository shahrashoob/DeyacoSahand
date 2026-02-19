<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachineAllocationModificationChangeGradeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('machine_allocation_modification_change_grade', function (Blueprint $table) {
            $table->id();
            $table->foreignId("machine_allocation_modification_id");
            $table->foreignId("product_id")->comment("ضایعات مواد اولیه برگشت شده");
            $table->foreignId("degree_id")->comment("درجه مواد اولیه برگشت شده ");
            $table->foreignId("sub_packing_form_number")->comment("تعداد بسته بندی های فرعی");
            $table->foreignId("packing_form_id")->nullable()->comment("بسته بندی جدید ایجاد شده برای ضایعات");
            $table->double("gross_weight",15,7)->comment("وزن ناخالص مواد اولیه");
            $table->double("weight",15,7)->comment("وزن مواد اولیه");
            $table->double("amount",15,7)->comment("مقدار اصلی مواد اولیه");
            $table->timestamps();
        });

        DB::statement("ALTER TABLE `machine_allocation_modification_change_grade` comment 'در زمان ثبت برگشت مواد اولیه، اطلاعات ضایعات مواد اولیه برگشت شده در این جدول ذخیره می گردد'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('machine_allocation_modification_wastage');
    }
}
