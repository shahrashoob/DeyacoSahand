<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFactoryEntryFormsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create( 'factory_entry_forms', function ( Blueprint $table ) {
            $table->id();
            $table->string("code")->comment("Deyaco Entering the Factory");
            $table->foreignId("factory_entry_type_id");
            $table->foreignId("other_id");
            $table->foreignId( "factory_id" )->default(1)->comment( "کارخانه محل ورود" );
            $table->foreignId("status_id")->comment("StatusType:6040");
            $table->foreignId("packing_type_id");
            $table->foreignId("form_id");
            $table->integer("number_of_packing_form")->comment("تعداد کل بسته بندی ها");
            $table->double("amount",15,4)->comment("مقدار کل اصلی بسته بندی ها");
            $table->double("sub_amount",15,4)->nullable()->comment("مقدار کل فرعی بسته بندی ها");


            $table->timestamps();
        } );
        DB::statement("ALTER TABLE `factory_entry_forms` comment 'در این جدول لیست فرم های ورود به کارخانه ذخیره می گردد.'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists( 'factory_entry_forms' );
    }
}
