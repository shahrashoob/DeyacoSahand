<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHasArticleChangeToAllocation extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'allocations', function ( Blueprint $table ) {
            $table->boolean( "has_article_change" )->nullable()->comment( "آیا در تخصیص جدید طرح عوض شده است؟" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'allocation', function ( Blueprint $table ) {
            //
        } );
    }
}
