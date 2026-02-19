<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductRequestFormImportantStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_request_form_important_status', function (Blueprint $table) {
            $table->id();

            $table->foreignId("product_request_form_id");
            $table->foreignId("form_id");

            $table->foreignId( "applicant_type_id" )->nullable();
            $table->foreignId( "applicant_id" )->nullable();

            $table->dateTime( "create_exit_form_at" )->nullable()->comment( "تاریخ ثبت برگ خروج" )->nullable();
            $table->dateTime( "confirm_draft_form_at" )->nullable()->comment( "تاریخ تایید پیش نویس مالی" )->nullable();
            $table->dateTime( "confirm_final_form_at" )->nullable()->comment( "تاریخ تایید نهایی مالی" )->nullable();
            $table->dateTime( "loading_at" )->nullable()->comment( "تاریخ ارسال بار" )->nullable();
            $table->dateTime( "confirm_guarding_at" )->nullable()->comment( "تاریخ تایید نگهبانی" )->nullable();
            $table->dateTime( "confirm_applicant_at" )->nullable()->comment( "تاریخ تایید درخواست کننده" )->nullable();

            $table->timestamps();
        });

        DB::statement("ALTER TABLE `product_request_form_important_status` comment 'تاریخ های مهم برگ های خروج از انبار'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_request_form_important_status');
    }
}
