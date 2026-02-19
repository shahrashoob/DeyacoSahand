<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePostEvaluationIndicatorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_evaluation_indicators', function (Blueprint $table) {
            $table->id();
            $table->foreignId("post_id")->comment('پست');
            $table->foreignId("evaluation_type_id")->comment('نوع ارزیابی');
            $table->foreignId("evaluation_indicator_id")->comment('شاخص');
            $table->integer("weight")->comment('ضریب شاخص');
            $table->integer("priority_number")->comment('اولویت ارزیابی');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `post_evaluation_indicators` comment 'پست-شاخص های ارزیابی'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('post_evaluation_indicators');
    }
}
