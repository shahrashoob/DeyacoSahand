<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInputFormIdToRejectProductForm extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'reject_product_forms', function ( Blueprint $table ) {
            //
            $table->foreignId( "input_form_id" )->nullable()->comment( "فرم ورود به انبار برای کالاهای مرجوعی" );
            $table->foreignId( "exit_form_id" )->nullable()->comment( "فرم خروج از انباری که از روی آن فرم مرجوعی ثبت شده است." );
            $table->dropColumn( "form_id" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'reject_product_form', function ( Blueprint $table ) {
            //
        } );
    }
}
