<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPostEvaluationIdToPostEvaluationIndicators extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('post_evaluation_indicators', function (Blueprint $table) {
            $table->foreignId("post_evaluation_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('post_evaluation_indicators', function (Blueprint $table) {
            //
        });
    }
}
