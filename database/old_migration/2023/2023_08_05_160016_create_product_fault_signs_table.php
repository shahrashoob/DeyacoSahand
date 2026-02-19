<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductFaultSignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_fault_signs', function (Blueprint $table) {
            $table->id();
            $table->string("caption");
            $table->timestamps();
        });

        DB::statement("ALTER TABLE `product_fault_signs` comment 'لیست نمودهای برونی برای نقص های کالا'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_fault_signs');
    }
}
