<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRawMaterialRequestAlgorithmTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create( 'raw_material_request_algorithm_types', function ( Blueprint $table ) {
            $table->id();
            $table->string( "caption" );
            $table->timestamps();
        } );

        DB::statement("ALTER TABLE `raw_material_request_algorithm_types` comment 'در این جدول انواع الگوریتم های درخواست مواد اولیه برای ماشین که اسکریپت 1007 از آن استفاده می کند، نمایش داده می شود.'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists( 'raw_material_request_algorithm' );
    }
}
