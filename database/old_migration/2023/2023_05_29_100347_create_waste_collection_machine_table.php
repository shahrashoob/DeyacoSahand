<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWasteCollectionMachineTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create( 'waste_collection_machine', function ( Blueprint $table ) {
            $table->id();
            $table->foreignId( "waste_collection_id" );
            $table->foreignId( "machine_id" );
            $table->foreignId( "allocation_id" )->nullable()->comment( "تخصیص جاری ماشین" );
            $table->foreignId( "machine_log_id" )->nullable()->comment( "لاگ جمع آوری ضایعات در ماشین" );
            $table->timestamps();
        } );

        DB::statement("ALTER TABLE `waste_collection_machine` comment 'به ازای هر بار جمع آوری ضایعات، این ضایعات برای تعدادی ماشین است که در این جدول نگهداری میشود.'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists( 'waste_collection_machine' );
    }
}
