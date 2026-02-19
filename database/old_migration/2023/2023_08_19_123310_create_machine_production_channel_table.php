<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMachineProductionChannelTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create( 'machine_production_channel', function ( Blueprint $table ) {
            $table->id();
            $table->foreignId( "machine_id" );
            $table->foreignId( "production_channel_id" )->comment( "کانال تولید " );
            $table->foreignId( "status_id" )->comment( "نوع وضعیت ها با شناسه 3358" );
            $table->float( "min_capacity" )->nullable()->comment( "حداقل ظرفیت کانال تولید" );
            $table->float( "max_capacity" )->nullable()->comment( "حداکثر ظرفیت کانال تولید" );
            $table->float( "remaining_capacity" )->nullable()->comment( "مقدار باقی مانده از کانال تولید" );
            $table->timestamps();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists( 'machine_production_channel' );
    }
}
