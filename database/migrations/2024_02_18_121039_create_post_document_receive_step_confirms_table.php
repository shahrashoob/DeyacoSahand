<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostDocumentReceiveStepConfirmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_document_receive_step_confirms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('receive_document_step_id');
            $table->foreignId('post_id');
            $table->integer('confirm_type')->default(1)->comment('الزامی بودن در هر مرحله در خواست همکاری در پست یک الزامی در مرحله اول دو الزامی در مرحله دوم و سه عدم تایید ');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('post_document_receive_step_confirms');
    }
}
