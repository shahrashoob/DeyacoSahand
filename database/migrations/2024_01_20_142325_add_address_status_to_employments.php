<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAddressStatusToEmployments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employments', function (Blueprint $table) {
            $table->foreignId("status_personal_id")->default(4641401)->comment("وضعیت اطلاعات فردی");
            $table->foreignId("status_address_id")->default(4641401)->comment("وضعیت ادرس");
            $table->foreignId("status_academic_degree_id")->default(4641401)->comment(" تحصیل وضعیت");
            $table->foreignId("status_job_information_id")->default(4641401)->comment("وضعیت شغلی");
            $table->foreignId("status_educational_course_id")->default(4641401)->comment("وضعیت دوره اموزشی");

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
            //
        });
    }
}
