<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRejectProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reject_product_forms', function (Blueprint $table) {
            $table->id();
            $table->string("code")->nullable();
            $table->foreignId("applicant_type_id");
            $table->foreignId("applicant_id");
            $table->foreignId("status_id");
            $table->foreignId("form_id");
            $table->foreignId("order_id");
            $table->foreignId("reject_product_reason_type_id")->comment("دلیل مرجوع کردن بسته ها");
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
        Schema::dropIfExists('reject_product');
    }
}
