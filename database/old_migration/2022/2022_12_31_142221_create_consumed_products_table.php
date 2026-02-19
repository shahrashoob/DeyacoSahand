<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConsumedProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consumed_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId("product_id");
            $table->foreignId("material_id")->comment("شناسه کالای مصرفی");
            $table->timestamps();
        });

        DB::statement("ALTER TABLE `consumed_products` comment 'لیست کالاهای مصرف شده به ازای هر کالا'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('consumed_products');
    }
}
