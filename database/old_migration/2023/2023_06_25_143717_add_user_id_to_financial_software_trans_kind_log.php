<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToFinancialSoftwareTransKindLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('financial_software_trans_kind_log', function (Blueprint $table) {
            //
            $table->foreignId("user_id");
            $table->foreignId("message_id")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('financial_software_trans_kind_log', function (Blueprint $table) {
            //
        });
    }
}
