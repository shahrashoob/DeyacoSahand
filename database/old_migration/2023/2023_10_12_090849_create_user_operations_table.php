<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserOperationsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create( 'user_operations', function ( Blueprint $table ) {
            $table->id();
            $table->foreignId( "user_id" );
            $table->date( "current_date" )->comment( "روز جاری" );

            $table->integer( "allowed_earlier_time_for_entry" )->comment( "زمان تعجیل در خروج مجاز  (ثانیه)" );
            $table->integer( "allowed_delay_time_for_entry" )->comment( "زمان تاخیر در ورود مجاز  (ثانیه)" );
            $table->integer( "allowed_earlier_time_for_exit" )->comment( "زمان تعجیل در خروج مجاز  (ثانیه)" );
            $table->integer( "allowed_delay_time_for_exit" )->comment( "زمان تاخیر در خروج مجاز  (ثانیه)" );
            $table->integer( "present_in_organ" )->comment( "کل مدت حضور (ثانیه)" );
            $table->integer( "not_allowed_present_in_organ" )->comment( "زمان حضور غیرمجاز (ثانیه)" );
            $table->integer( "allowed_present_in_organ" )->comment( "زمان حضور مجاز (ثانیه)" );
            $table->integer( "allowed_operation" )->comment( "زمان کارکرد - طبق آیین نامه تامین اجتماعی (ثانیه)" );
            $table->integer( "leave" )->comment( "زمان مرخصی (ثانیه)" );
            $table->integer( "overtime" )->comment( "زمان اضافه کار" );
            $table->integer( "mission" )->comment( "زمان ماموریت" );

            $table->integer( "morning" )->comment( "زمان کار در شیف صبح" );
            $table->integer( "afternoon" )->comment( "زمان کار در شیفت ظهر" );
            $table->integer( "night" )->comment( "زمان کار در شیفت شب" );

            $table->timestamps();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists( 'user_operations' );
    }
}
