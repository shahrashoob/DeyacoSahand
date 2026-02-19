<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductRequestFormSessionDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_request_form_packing_session_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId("product_request_form_id");
            $table->longText("data");
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `product_request_form_packing_session_data` comment 'در این جدول بسته بندی هایی که به صورت موقت برای یک درخواست کالا از انبار انتخاب شده اند، نگهداری میش ود.'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_request_form_packing_session_data');
    }
}
