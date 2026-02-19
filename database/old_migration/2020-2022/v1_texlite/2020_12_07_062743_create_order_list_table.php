<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateOrderListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_list', function (Blueprint $table) {
            $table->id();

            $table->integer("order_id")->default(0);
            $table->integer("product_id")->default(-100);
            $table->integer("customer_id")->nullable();
            $table->integer("production_card_id")->nullable();

            $table->integer("call_id")->nullable();

            $table->integer("carton")->nullable();
            $table->integer("number_in_carton")->nullable();
            $table->integer("amount")->default(0)->comment("number * number_in_carton");

            $table->integer("unit_id")->nullable();

            $table->integer("order_status_id")->default(-100)->comment("status_type=100, جاری، لغو شده");

            $table->integer("wherehouse_id")->nullable();

            $table->datetime("order_datetime")->nullable();

            $table->integer("priority_id")->default(3);

            $table->string("prefactor_number")->nullable()->comment(" شماره فاکتور");
            $table->integer("order_type_id")->default(-100)->comment("100/200 نوع رویدادر");


            $table->integer("erp_status_id")->default(-100)->comment("if(jari)=>waiting else finish before/ status Type 300");

            $table->integer("description_sheet_id")->nullable()->comment("شرح برگه");
            $table->integer("description_request_id")->nullable()->comment("شرخ درخواست");

            // Calculate Item
            $table->double('amount_sent', 15, 2)->default(0)->comment("sent amount");
            $table->double("amount_remaining",15,2)->default(0)->comment("Remaining amount");
            $table->double('amount_ep', 15, 2)->default(0)->comment("amount of extra production in product");

            $table->double("inventory",15,2)->default(0)->comment("inventory in product_inventory tbl");
            $table->double('sum_wpc', 15, 2)->default(0)->comment(" sum ( product card ) where status is waiting ");
            $table->double('sum_wo', 15, 2)->default(0)->comment(" sum (amount_remaining) where status is waiting or sent with remaining");

            $table->double('bp', 15, 2)->default(0)->comment("batch of product ");
            $table->double('mi', 15, 2)->default(0)->comment("min of inventory ");
            $table->double('mp', 15, 2)->default(0)->comment("min of production ");
            $table->double('po', 15, 2)->default(0)->comment("sum_wo + mi - ( sum_wpc+inventory)");
            $table->double('pc', 15, 2)->default(0)->comment("max( pc + amount_ep , (po+amount_ep/bp)*bp");
            $table->double('ap', 15, 2)->default(0)->comment("ap = pc-po");

            $table->integer("loadable_status_id")->nullable()->comment("Loadable Status");

            $table->integer("status_id")->default(300)->comment("وضعیت پردازش سطر ");
            $table->text("error")->nullable();


            $table->integer("from_order_id")->default(0)->comment("کد سفارش که از کارت دیگری آمده ");
            $table->integer("from_order_list_id")->default(0)->comment("کد order_list که از کارت دیگری آمده ");
            $table->integer("from_production_card_id")->default(0)->comment("کد کارت تولدی که از کارت دیگری آمده ");


            $table->datetime("created_at")->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->datetime("updated_at")->default(DB::raw('CURRENT_TIMESTAMP'));

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_list');
    }
}
