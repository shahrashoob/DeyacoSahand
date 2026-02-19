<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriorityNumberToAllocation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('allocations', function (Blueprint $table) {
            //
            $table->integer("priority_number")->default(1)->comment("اولویت تخصیص: این اولویت توسط سیستم مشخص می شود و ممکن است در زمان تخصیص کارت نمونه گیری اولویت عوض شود.");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('allocation', function (Blueprint $table) {
            //
        });
    }
}
