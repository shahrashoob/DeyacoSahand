<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateEvaluationFormExportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evaluation_form_exports', function (Blueprint $table) {
            $table->id();
            $table->foreignId("post_id")->comment('پست');
            $table->foreignId("user_id")->nullable()->comment('کاربر');
            $table->foreignId("evaluation_form_id")->comment('فرم ارزیابی');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `evaluation_form_exports` comment 'ارزیاب-فرم ارزیابی'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('evaluation_form_exports');
    }
}
