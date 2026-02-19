<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinancialOperationPatternsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('financial_operation_patterns', function (Blueprint $table) {
            $table->id();
            $table->string("caption");
            $table->foreignId("financial_operation_pattern_type_id");
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `financial_operation_patterns` comment 'لیست الگوهای مالی تعریف شده'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('financial_operation_patterns');
    }
}
