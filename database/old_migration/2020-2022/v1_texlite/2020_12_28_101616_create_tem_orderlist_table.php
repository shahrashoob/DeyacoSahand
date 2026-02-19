<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTemOrderlistTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tem_orderlist', function (Blueprint $table) {
            $table->id();

            $table->string("order_code")->nullable();
            $table->string("order_series")->nullable();

            $table->string("product_code")->nullable();
            $table->string("customer_code")->nullable();
            $table->string("production_card_code")->nullable();
            
            
            $table->integer("carton")->nullable();
            $table->integer("number_in_carton")->nullable();
            $table->integer("amount")->default(0)->comment("number * number_in_carton");
           

            $table->integer("order_status_id")->default(-100)->comment("status_type=100, جاری، لغو شده");

            $table->integer("wherehouse_id")->nullable();

            $table->datetime("order_datetime")->nullable();

            $table->integer("priority_id")->default(3);

            $table->string("prefactor_number")->nullable()->comment(" شماره فاکتور");
            $table->integer("order_type_id")->default(-100)->comment("100/200 نوع رویدادر");
            
            
            $table->integer("erp_status_id")->default(-100)->comment("if(jari)=>waiting else finish before/ status Type 300");
            
            $table->string("description_sheet")->nullable()->comment("شرح برگه");
            $table->string("description_request")->nullable()->comment("شرخ درخواست");

            // Calculate Item
            $table->double('amount_sent', 15, 2)->default(0)->comment("sent amount");
            $table->double("amount_remaining",15,2)->default(0)->comment("Remaining amount");
            $table->double('amount_ep', 15, 2)->default(0)->comment("amount of extra production in product");

            $table->double("inventory",15,2)->default(0)->comment("inventory in product_inventory tbl");
            $table->double('sum_wpc', 15, 2)->default(0)->comment(" sum ( product card ) where status is waiting ");
            $table->double('sum_wo', 15, 2)->default(0)->comment(" sum (amount_remaining) where status is waiting or sent with remaining");

            $table->double('mi', 15, 2)->default(0)->comment("min of inventory ");
            $table->double('mp', 15, 2)->default(0)->comment("min of production ");
            $table->double('po', 15, 2)->default(0)->comment("sum_wo + mi - ( sum_wpc+inventory)");
            $table->double('pc', 15, 2)->default(0)->comment("max( pc + amount_ep , (po+amount_ep/bp)*bp");
            $table->double('ap', 15, 2)->default(0)->comment("ap = pc-po");

            $table->integer("loadable_status_id")->nullable()->comment("Loadable Status for AI");

            $table->integer("status_id")->default(300)->comment("وضعیت پردازش سطر ");
            $table->text("error")->nullable();


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tem_orderlist');
    }
}
