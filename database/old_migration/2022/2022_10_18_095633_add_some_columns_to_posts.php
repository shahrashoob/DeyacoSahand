<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSomeColumnsToPosts extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table( 'posts', function ( Blueprint $table ) {
            //
            $table->integer( "for_leave_a_few_top_levels_must_confirm" )->
            default( 1 )->
            comment( "برای مرخصی چند سطح بالایی باید تایید کنند " );

            $table->integer( "for_leave_a_few_top_levels_must_confirm_time" )->
            default( 8 )->
            comment( "برای مرخصی بیش از چند ساعت، چند سطح بالایی باید تایید کنند " );

            $table->integer( "for_leave_a_few_top_levels_must_confirm_time_level" )->
            default( 1 )->
            comment( "برای مرخصی بیش از --- ساعت، چند سطح بالایی باید تایید کنند " );

            $table->integer( "for_leave_required_to_replace_person" )->
            default( 0 )->
            comment( "برای مرخصی آیا نیاز به جانشین دارد؟ " );


            $table->integer( "emergency_leave_number_in_year" )->
            default( 1 )->
            comment( "تعداد مرخصی اضطراری در سال چند دفعه می باشد " );


            $table->integer( "marriage_leave_time_in_year" )->
            default( 0 )->
            comment( "مقدار مرخصی ازدواج در سال (ساعت) " );

            $table->integer( "death_of_relatives_leave_time_in_year" )->
            default( 0 )->
            comment( "مقدار مرخصی فوت اقوام در سال (ساعت) " );


        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table( 'posts', function ( Blueprint $table ) {
            //
        } );
    }
}
