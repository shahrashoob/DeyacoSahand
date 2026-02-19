<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNeedToConfirmationForFixToMachineFaults extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('machine_faults', function (Blueprint $table) {
            //
            $table->integer("need_to_confirmation_for_fix")->default(0)->comment("آیا نیاز به تایید رفع نقص وجود دارد؟");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('confirmation_for_fix_to_machine_faults', function (Blueprint $table) {
            //
        });
    }
}
