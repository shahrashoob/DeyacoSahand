<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSomeColsToUserOperations extends Migration
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
            $table->integer("internal_absence")->comment("غیبت داخلی");
            $table->integer("legal_absence")->comment("غیبت قانونی");
            $table->integer("legal_entitlement")->comment("مرخضی استحقاقی قانونی");

            $table->integer("leave_type_1")->comment("مرخصی استحقاقی داخلی");
            $table->integer("leave_type_2")->comment(" مرخصی اضطراری داخلی");
            $table->integer("leave_type_3")->comment(" مرخصی استعلاجی داخلی");
            $table->integer("leave_type_4")->comment(" مرخصی ازدواج داخلی");
            $table->integer("leave_type_5")->comment(" مرخصی فوت اقوام درجه یک داخلی");
            $table->integer("leave_type_6")->comment(" مرخصی تشویقی  داخلی");
            $table->integer("leave_type_7")->comment(" مرخصی بدون حقوق داخلی");
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
