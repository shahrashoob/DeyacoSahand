<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractorSupplyTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contractor_supply_types', function (Blueprint $table) {
            $table->id();
            $table->string("caption");
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `contractor_supply_types` comment 'روش های ارسال ماده اولیه از پیمانکار به کارفرما برای کالاهای کارمزدی'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('consumed_product_supply_types');
    }
}
