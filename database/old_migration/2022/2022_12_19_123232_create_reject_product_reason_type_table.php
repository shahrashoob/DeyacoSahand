<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRejectProductReasonTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reject_product_reason_types', function (Blueprint $table) {
            $table->id();
            $table->string("caption");
        });
        DB::statement("ALTER TABLE `reject_product_reason_types` comment 'دلایل مرجوعی کالا'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reject_product_reason_type');
    }
}
