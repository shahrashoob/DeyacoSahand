<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAllowFilterMenuToPostEntryStatus extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'post_entry_status', function ( Blueprint $table ) {
            //
            $table->integer( "allow_filter_menu" )->default( 0 )->comment("در صورتی که پست اجازه مشاهده منو ها را داشته باشد، آیا به همه منو ها دسترسی دارد، یا با توجه به شیفت فیلتر کنند ");
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'post_entry_status', function ( Blueprint $table ) {
            //
        } );
    }
}
