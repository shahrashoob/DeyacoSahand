<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBarcodeLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('barcode_links', function (Blueprint $table) {
            $table->id();
            $table->string("code");
            $table->string("caption");
            $table->integer("number_of_visits")->default(0)->comment("تعداد بازدید");
            $table->integer("number_of_register")->default(0)->comment("تعداد عضویت");
            $table->foreignId("status_id")->default("1200")->comment("وضعیت فعال بودن: 1100");
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
        Schema::dropIfExists('barcode_links');
    }
}
