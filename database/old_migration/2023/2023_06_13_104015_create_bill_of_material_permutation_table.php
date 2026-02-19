<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillOfMaterialPermutationTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create( 'bill_of_material_permutation', function ( Blueprint $table ) {
            $table->id();
            $table->foreignId( "bill_of_material_id" );
            $table->foreignId( "product_id" );
            $table->double( "weight", 15, 8 )->comment( "وزن کالای جایگردی" );
            $table->integer( "is_original_product" )->comment( "آیا این کالا از مواد اولیه اصلی در BOM ایجاد می شود." );
            $table->timestamps();
        } );

        DB::statement( "ALTER TABLE `bill_of_material_permutation` comment 'هربا توجه به BOM کالا ممکن است کالای اصلی یا یک کالای جایگرد از کالاهای جایگزین مصرف را تولید نماید.'" );

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists( 'bill_of_material_permutation' );
    }
}
