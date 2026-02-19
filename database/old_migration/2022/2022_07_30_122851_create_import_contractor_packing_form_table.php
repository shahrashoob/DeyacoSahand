<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImportContractorPackingFormTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('import_contractor_packing_form', function (Blueprint $table) {
            $table->id();

            $table->foreignId("machine_allocation_id");

            $table->integer("parent_packing_form_row")->nullable()->comment("ردیف بسته بندی اصلی");
            $table->foreignId("parent_packing_form_id")->nullable()->comment("شناسه نوع بسته بندی اصلی");

            $table->integer("parent_packing_form_carrier")->nullable()->comment("حامل بسته بندی اصلی");
            $table->foreignId("parent_packing_form_carrier_id")->nullable()->comment("شناسه حامل بسته بندی اصلی");

            $table->integer("packing_form_row")->nullable()->comment("ردیف بسته بندی فرعی");
            $table->foreignId("packing_form_id")->nullable()->comment("شناسه نوع بسته بندی فرعی");

            $table->foreignId("packing_form_carrier")->nullable()->comment("حامل بسته بندی فرعی");
            $table->foreignId("packing_form_carrier_id")->nullable()->comment("شناسه حامل بسته بندی فرعی");


            $table->float("amount")->nullable()->comment("مقدار اصلی");
            $table->float("sub_amount")->nullable()->comment("مقدار فرعی");
            $table->float("sub_amount2")->nullable()->comment("مقدار فرعی2");

            $table->string("degree_code")->nullable()->comment("کد درجه");
            $table->foreignId("degree_id")->nullable()->comment("شناسه درجه");

            $table->string("lot_number_code")->nullable()->comment("کد همبافت");
            $table->foreignId("lot_number_id")->nullable()->comment("شناسه همبافت");

            $table->string("error");
            $table->string("warning");

            $table->foreignId("parent_packing_type_id")->nullable()->comment("نوع  بسته بندی اصلی");
            $table->foreignId("packing_type_id")->nullable()->comment("نوع بسته بندی فرعی");
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
        Schema::dropIfExists('import_contractor_packing_form');
    }
}
