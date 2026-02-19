<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInternalLeaveToUserOperations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_operations', function (Blueprint $table) {
            //
            $table->integer("internal_leave")->default(0)->after("leave")->comment(" مرخصی داخلی (ثانیه)");
            $table->foreignId("leave_type_id")->nullable()->after("leave")->comment(" نوع مرخصی");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_operations', function (Blueprint $table) {
            //
        });
    }
}
