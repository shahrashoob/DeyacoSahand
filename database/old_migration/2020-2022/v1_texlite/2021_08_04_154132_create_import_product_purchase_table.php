<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImportProductPurchaseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('import_product_purchase', function (Blueprint $table) {
            $table->id();
            $table->string( "product_code" );
            $table->string( "product_id" );
            $table->string( "min_buy" );
            $table->string( "max_buy" );
            $table->string( "batch_buy" );
            $table->string( "warehouse_id" );
            $table->string( "warehouse_code" );
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
        Schema::dropIfExists('import_product_purchase');
    }
}
