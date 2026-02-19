<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackingFormItemStatusTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create( 'packing_form_item_status', function ( Blueprint $table ) {
            $table->id();
            $table->foreignId( "packing_form_id" );
            $table->foreignId( "packing_form_item_id" );
            $table->foreignId( "product_id" );
            $table->foreignId( "goods_kind_id" );

            $table->double( "amount" );
            $table->foreignId( "applicant_type_id" )->nullable();
            $table->foreignId( "applicant_id" )->nullable();

            $table->dateTime( "packing_created_at" )->comment( "تاریخ ایجاد بسته بندی" )->nullable();
            $table->dateTime( "completion_and_delivery_to_warehouse_at" )->comment( "تاریخ تکمیل و تحویل به انبار" )->nullable();
            $table->dateTime( "confirm_warehouse_at" )->comment( "تاریخ تایید انبار" )->nullable();
            $table->dateTime( "create_transport_item_at" )->comment( "تاریخ عدل بندی" )->nullable();
            $table->dateTime( "create_exit_form_at" )->comment( "تاریخ ثبت برگ خروج" )->nullable();
            $table->dateTime( "confirm_draft_form_at" )->comment( "تاریخ تایید پیش نویس مالی" )->nullable();
            $table->dateTime( "confirm_final_form_at" )->comment( "تاریخ تایید نهایی مالی" )->nullable();
            $table->dateTime( "loading_at" )->comment( "تاریخ ارسال بار" )->nullable();
            $table->dateTime( "confirm_guarding_at" )->comment( "تاریخ تایید نگهبانی" )->nullable();
            $table->dateTime( "confirm_applicant_at" )->comment( "تاریخ تایید درخواست کننده" )->nullable();

            $table->timestamps();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists( 'packing_form_item_status' );
    }
}
