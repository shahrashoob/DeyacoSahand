<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackingFormSubPackingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packing_form_sub_packing', function (Blueprint $table) {
            $table->id();
            $table->foreignId("packing_form_id");
            $table->foreignId("parent_packing_form_id")->comment("شناسه بسته بندی پدر");
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `packing_form_sub_packing` comment 'در این جدول لیست بسته بندی هایی که داخل یک بسته بندی است نمایش داده می شود.'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('packing_form_sub_packing');
    }
}
