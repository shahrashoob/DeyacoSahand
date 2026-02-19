<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWasAnyInfoInIcInEmployments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employments', function (Blueprint $table) {
            $table->integer('was_any_info_in_ic')->default(0)->comment('ایا اطلاعاتی که به ای   سی رفته وجو داشته است یا نه/ صفر و یک که صفر وجود ندارد یک وجود دارد');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employments', function (Blueprint $table) {

        });
    }
}
