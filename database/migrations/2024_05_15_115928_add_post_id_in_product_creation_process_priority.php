<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPostIdInProductCreationProcessPriority extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_creation_process_priority', function (Blueprint $table) {
            $table->foreignId('post_id')->nullable()->after('before_status_id')->comment("پست اطلاع رسانی");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_creation_process_priority', function (Blueprint $table) {
            //
        });
    }
}
