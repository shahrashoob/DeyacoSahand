<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachineAllocationModificationForms extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create( 'machine_allocation_modification_form', function ( Blueprint $table ) {
            $table->id();
            $table->foreignId( "machine_allocation_modification_id" );
            $table->foreignId( "product_request_form_id" );
            $table->foreignId( "input_form_id" )->comment( "فرم ورود" );
            $table->foreignId( "output_form_id" )->comment( "برگ خروج" );
            $table->foreignId( "input_form_status_id" )->comment( "وضعیت تایید/عدم تایید فرم ورود" );
            $table->timestamps();
        } );
        DB::statement("ALTER TABLE `machine_allocation_modification_form` comment 'در این جدول اطلاعات فرم ورود و برگ خروج کشیده شده برای نقل و انتقال ذخیره می گردد'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists( 'machine_allocation_modification_forms' );
    }
}
