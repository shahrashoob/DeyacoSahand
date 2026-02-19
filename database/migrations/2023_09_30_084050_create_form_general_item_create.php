<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormGeneralItemCreate extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create( 'form_general_item', function ( Blueprint $table ) {
            $table->id();
            $table->foreignId( "form_id" )->nullable();
            $table->foreignId( "production_form_item_id" )->nullable()->comment( "شناسه آیتم فرم تولیدی که منجر به ثبت این آیتم کلی انبار شده است." );
            $table->foreignId( "machine_allocation_id" );
            $table->foreignId( "product_id" );
            $table->foreignId( "degree_id" );
            $table->foreignId( "lot_number_id" );
            $table->foreignId( "packing_type_id" );
            $table->integer( "status_id" )->comment("5002: وضعیت جدول FormGeneralItem");
            $table->integer( "packing_form_number" );
            $table->double( "amount",15,7 )->comment( "مقدار کالا " );
            $table->double( "sub_amount",15,7 )->nullable()->comment( "مقدار  فرعی کالا " );
            $table->double( "price", 15, 2 )->comment( "مبلغ کل " );
            $table->double( "tax_price", 15, 2 )->comment( "ارزش افزوده / مالیات  " );
            $table->double( "total_price_with_tax", 15, 2 )->comment( "مبلع کل با مالیات" );
            $table->timestamps();
        } );

        DB::statement("ALTER TABLE `form_general_item` comment 'لیست آیتم های کلی فرم انبار، هر آیتم کلی منجر به یک یا چند آیتم فرم تولید می شود.'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists( 'form_general_item_create' );
    }
}
