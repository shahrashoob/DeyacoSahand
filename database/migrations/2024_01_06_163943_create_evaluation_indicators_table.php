<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateEvaluationIndicatorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evaluation_indicators', function (Blueprint $table) {
            $table->id();
            $table->string("caption")->comment('نام شاخص');
            $table->foreignId("evaluation_completion_type_id")->default(1)->comment('نوع تکمیل');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `evaluation_indicators` comment 'شاخص های ارزیابی'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('evaluation_indicators');
    }
}
