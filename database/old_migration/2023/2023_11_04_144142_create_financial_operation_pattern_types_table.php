<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinancialOperationPatternTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('financial_operation_pattern_types', function (Blueprint $table) {
                $table->id();
                $table->string("caption");
                $table->integer("financial_credit_or_debit")->comment("نوع طرف حساب: 1-بدهکار، 2-بستانکار");
                $table->timestamps();
            });
            DB::statement("ALTER TABLE `financial_operation_pattern_types` comment 'انواع الگوهای مالی: فروش، کالا و ...'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('financial_operation_pattern_types');
    }
}
