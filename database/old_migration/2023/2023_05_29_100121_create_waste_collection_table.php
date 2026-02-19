<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWasteCollectionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('waste_collections', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id");
            $table->foreignId("product_id")->comment("کالای ضایعاتی");
            $table->double("gross_weight");
            $table->double("weight");
            $table->double("amount");
            $table->foreignId("packing_form_id")->comment("بسته بندی که برای کالا ایجاد شده است.");
            $table->timestamps();
        });

        DB::statement("ALTER TABLE `waste_collections` comment 'به ازای هر بار اجرا شدن ماژول جمع آوری ضایعات یک ردیف به این جدول اضافه می شود'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('waste_collection');
    }
}
