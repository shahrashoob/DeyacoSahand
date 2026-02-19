<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserJobInformationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_job_informations', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id");
            $table->date('start_date_of_work')->comment('تاریخ شروع کار');
            $table->date('end_date_of_work')->nullable()->comment('تاریخ پایان کار');
            $table->string('post_caption')->comment('پست سازمانی که مشغول بوده');
            $table->string('company_name_of_work')->comment('نام شرکتی که مشغول بوده');
            $table->string('address_of_work')->comment('آدرس شرکتی که مشغول بوده');
            $table->string('identifier_name')->nullable()->comment('نام معرف');
            $table->string('identification_number')->nullable()->comment('شماره معرف');
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
        Schema::dropIfExists('user_job_informations');
    }
}
