<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFabricRawDesignFormLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fabric_raw_design_form_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId( "fabric_raw_design_form_id" );
            $table->boolean( "design_available" )->nullable()->comment( "آیا طراحی موجود است" );
            $table->boolean( "it_has_pinning" )->nullable()->comment( "آیا لامل ریزی دارد" );
            $table->boolean( "warps_is_in_warehouse" )->nullable()->comment( "آیا چله در انبار هست؟" );
            $table->boolean( "need_to_convert" )->nullable()->comment( "نیار به تبدیل دارد؟" );

            $table->foreignId( "status_id" );
            $table->foreignId( "message_id" )->nullable();
            $table->foreignId( "user_id" );

            $table->datetime( "created_at" )->default( DB::raw( 'CURRENT_TIMESTAMP' ) );
            $table->datetime( "updated_at" )->default( DB::raw( 'CURRENT_TIMESTAMP' ) );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fabric_raw_design_form_logs');
    }
}
