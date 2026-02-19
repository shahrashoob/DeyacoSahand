<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeaveOvertimeConfirmationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leave_overtime_confirmation', function (Blueprint $table) {

            $table->id();

            $table->foreignId("leave_overtime_id");
            $table->foreignId("post_user_id");

            $table->foreignId("replace_post_id")->nullable()->comment("پست جانشین");
            $table->foreignId("replace_user_id")->nullable()->comment("شغل جانشین");

            $table->foreignId("current_confirm_post_id")->nullable()->comment("پستی که مرخصی در انتظار تایید اوست");
            $table->foreignId("current_confirm_user_id")->nullable()->comment("مافوقی که مرخصی در انتظار تایید اوست");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leave_overtime_confirmation');
    }
}
