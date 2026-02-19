<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateEvaluationFormIndicatorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evaluation_form_indicators', function (Blueprint $table) {
            $table->id();
            $table->foreignId("evaluation_indicator_id")->comment('شاخص ارزیابی');
            $table->foreignId("evaluation_form_id")->comment('فرم ارزیابی');
            $table->integer("value")->comment('نمره شاخص');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `evaluation_form_indicators` comment 'شاخص-فرم ارزیابی'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('evaluation_form_indicators');
    }
}
