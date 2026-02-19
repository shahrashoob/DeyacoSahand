<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeaveOvertimeGroupsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create( 'leave_overtime_groups', function ( Blueprint $table ) {
            $table->id();
            $table->string( "caption" )->comment( "نام گروه اضافه کاری یا مرخص/ماموریت" );
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists( 'leave_overtime_groups' );
    }
}
