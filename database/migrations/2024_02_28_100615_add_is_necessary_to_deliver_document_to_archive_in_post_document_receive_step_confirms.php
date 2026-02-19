<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsNecessaryToDeliverDocumentToArchiveInPostDocumentReceiveStepConfirms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('post_document_receive_step_confirms', function (Blueprint $table) {
            $table->integer('is_necessary_to_deliver_document_to_archive')->default(0)->comment('ایا تحویل مدارک به بایگانی الزامی هست یا خیر1الزامی0غیر الزامی');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('post_document_receive_step_confirms', function (Blueprint $table) {
            //
        });
    }
}
