<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPermissionTypeIdToButtonPost extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('button_post', function (Blueprint $table) {
            //
            $table->foreignId("permission_type_id")->default(1)->comment("نوع دسترسی");
            $table->foreignId("other_id")->nullable()->comment("شناسه جدولی که مربوط به دسترسی می باشد.");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('button_post', function (Blueprint $table) {
            //
        });
    }
}
