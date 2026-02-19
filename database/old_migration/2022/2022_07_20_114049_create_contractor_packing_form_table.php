<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractorPackingFormTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contractor_packing_form', function (Blueprint $table) {
            $table->id();
            $table->foreignId("contractor_id");
            $table->foreignId("machine_allocation_id");
            $table->foreignId("packing_form_id");
            $table->timestamps();

        });
        DB::statement("ALTER TABLE `contractor_packing_form` comment 'فرم های بسته بندی که در بخش ثبت تولید توسط پیمانکار ایجاد می گردد، برای شناسایی در این جدول نگهداری می شوند.'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contractor_packing_form');
    }
}
