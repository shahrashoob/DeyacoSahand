<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDocumentDeliveryTypeInPostDocumentType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('post_document_type', function (Blueprint $table) {
            $table->integer('document_delivery_type')->default(2)->comment("نوع تحویل مدارک(1-در زمان ثبت نام و2-بعد از انجام ثبت نام)");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('post_document_type', function (Blueprint $table) {
            //
        });
    }
}
