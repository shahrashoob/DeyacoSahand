<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNeedToConfirmationToMachineFaults extends Migration
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
            $table->integer("need_to_confirmation")->default(0)->comment("آیا بعد از اعلام نقص، نیاز به تایید دارد");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('confirmation_to_machine_faults', function (Blueprint $table) {
            //
        });
    }
}
