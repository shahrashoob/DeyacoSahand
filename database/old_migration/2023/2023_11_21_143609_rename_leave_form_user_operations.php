<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameLeaveFormUserOperations extends Migration
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
            $table->renameColumn("leave","legal_leave");
            $table->renameColumn("legal_entitlement","legal_entitlement_remove");
            $table->renameColumn("leave_type_1","leave_type_1_remove");
            $table->renameColumn("leave_type_2","leave_type_2_remove");
            $table->renameColumn("leave_type_3","leave_type_3_remove");
            $table->renameColumn("leave_type_4","leave_type_4_remove");
            $table->renameColumn("leave_type_5","leave_type_5_remove");
            $table->renameColumn("leave_type_6","leave_type_6_remove");
            $table->renameColumn("leave_type_7","leave_type_7_remove");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
