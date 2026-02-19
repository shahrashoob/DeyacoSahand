<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateEvaluationCompletionTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evaluation_completion_types', function (Blueprint $table) {
            $table->id();
            $table->string("caption")->comment('نوع تکمیل ارزیابی');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `evaluation_completion_types` comment 'نوع تکمیل فرم های ارزیابی'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('evaluation_completion_types');
    }
}
