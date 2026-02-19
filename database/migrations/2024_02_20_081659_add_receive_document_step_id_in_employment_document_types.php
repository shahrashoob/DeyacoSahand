<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReceiveDocumentStepIdInEmploymentDocumentTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employment_document_types', function (Blueprint $table) {
            $table->foreignId('receive_document_step_id');
            $table->foreignId('other_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employment_document_types', function (Blueprint $table) {
            //
        });
    }
}
