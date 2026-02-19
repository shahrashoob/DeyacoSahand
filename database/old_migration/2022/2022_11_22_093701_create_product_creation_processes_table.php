<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductCreationProcessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_creation_processes', function (Blueprint $table) {
            $table->id();
            $table->string("code")->nullable();
            $table->string("caption")->comment("نام پیشنهادی");
            $table->foreignId("user_id");
            $table->foreignId("status_id");
            $table->foreignId("method_of_sending_product_type_id")->comment("روش ارسال نمونه کالا به کارخانه");
            $table->foreignId("product_id")->nullable();
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
        Schema::dropIfExists('product_creation_processes');
    }
}
