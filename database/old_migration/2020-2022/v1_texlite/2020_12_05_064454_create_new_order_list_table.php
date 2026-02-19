<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewOrderListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_order_list', function (Blueprint $table) {
            $table->id();
            $table->integer("order_code")->nullable();
            $table->integer("series")->default(0);
            $table->integer("order_id")->default(0);

            $table->string("product_code")->nullable();
            $table->string("product_id")->default(-100);

            $table->string("customer_code")->nullable();
            $table->integer("customer_id")->nullable();
            $table->integer("carton")->nullable();
            $table->integer("number_in_carton")->nullable();
            $table->integer("amount")->default(0)->comment("number * number_in_carton");
           
            $table->string("unit_caption")->nullable();
            $table->string("unit_id")->nullable();

            $table->string("order_status")->nullable();
            $table->integer("order_status_id")->default(-100);

            $table->integer("wherehouse_id");
            $table->integer("wherehouse_code");


            $table->string("order_date_shamsi")->nullable();
            $table->datetime("order_datetime")->nullable();

            $table->integer("priority_id")->default(3);
            $table->string("priority_text")->nullable();

            $table->string("prefactor_number")->nullable()->comment(" شماره فاکتور");

            $table->integer("order_type_id")->default(-100)->comment("شناسه نوع رویدادر");
            $table->string("order_type")->default(-100)->comment("نوع رویدادر");

            $table->integer("erp_status_id")->default(-100)->comment("if(jari)=>waiting else finish before/ status Type 300");
            
            $table->integer("description_sheet_id")->nullable()->comment("شرح برگه");
            $table->integer("description_request_id")->nullable()->comment("شرخ درخواست");

            $table->text("error")->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('new_order_list');
    }
}
