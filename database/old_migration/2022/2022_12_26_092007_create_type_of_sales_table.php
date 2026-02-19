<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTypeOfSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('type_of_sale_of_products', function (Blueprint $table) {
            $table->id();
            $table->string("caption");
        });
        DB::statement("ALTER TABLE `type_of_sale_of_products` comment 'جدول نوع فروش کالا : فروش عادی، فروش کارمزدی'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('type_of_sales');
    }
}
