<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateRequestFromWarehouseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_from_warehouse', function (Blueprint $table) {
            $table->id();

            $table->integer("order_id")->default(0);
            $table->integer("order_list_id")->default(0);
            $table->string("product_id")->default(-100);
            $table->integer("customer_id")->nullable();
            $table->integer("production_card_id")->nullable();
            $table->integer("perchase_order_id")->nullable();


            $table->integer("call_id")->nullable();

            $table->integer("material_id");
            $table->integer("alternative_material_id")->nullable()->compact("کد محصول جایگزنی");

            $table->integer("percent_of_waste")->default(0);

            $table->double('amount', 15, 2)->default(0)->comment("مقدار مورد نیاز ");
            $table->double('amount_sent', 15, 2)->default(0)->comment("sent amount");
            $table->double("amount_remaining",15,2)->default(0)->comment("Remaining amount");

            $table->double('po', 15, 2)->default(0)->comment("(1+persent_of_wast) * bom_amount ");


            $table->double('pc', 15, 2)->default(0)->comment("(PO+MI+RI)-(I+IIW)");
             $table->double("inventory",15,2)->defualt(0)->comment("inventory in product_inventory tbl");

            $table->double('mi', 15, 2)->default(0)->comment("min of inventory ");
           $table->double("ri",15,2)->defualt(0)->comment("sum (amount_remaining) where status is waiting");
           $table->double('iiw', 15, 2)->default(0)->comment("in new version ");

           $table->double('mp', 15, 2)->default(0)->comment("min order ");

            $table->double('bp', 15, 2)->default(0)->comment("batch of product ");


            $table->integer("supply_type_id")->default(0)->comment("نوع تامین کننده: خرید، تولید داخل ");
            $table->double('ai', 15, 2)->default(0)->comment("(po) ");

            $table->integer("status_id")->nullable()->comment("");



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
        Schema::dropIfExists('request_from_warehouse');
    }
}
