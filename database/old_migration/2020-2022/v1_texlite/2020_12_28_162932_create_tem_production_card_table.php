<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTemProductionCardTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tem_production_card', function (Blueprint $table) {
            
            
            $table->integer("order_id")->default(0);
            $table->integer("order_list_id")->default(0);
            $table->integer("customer_id")->nullable();
            $table->integer("product_id")->nullable();

            
            $table->string("order_code")->default(0);
            $table->string("order_list_code")->default(0);
            $table->string("customer_code")->nullable();
            $table->string("product_code")->nullable();
            $table->string("line_code")->nullable();

            
            $table->string("line_id")->nullable();
            
            $table->string("line_product_id")->nullable();

            $table->string("serial")->nullable()->comment("سریال تولید");

            /**************** Order */
           
            $table->integer("set_up_time")->nullable()->comment(" زمان - دقیقه");
            $table->integer("down_time")->nullable()->comment("زمان دون تایم - دقیقه");


            $table->integer("unemployment_time")->nullable()->comment("زمان مجاز بیکاری - دقیقه");

            $table->integer("line_allocation")->nullable()->comment("تخصیص خط - دقیقه");
        
            $table->integer("production_time")->nullable()->comment("زمان تولید");
        

            $table->float("number")->nullable()->comment("تعداد ");
            $table->integer("number_in_carton")->defualt(1)->comment("تعداد در کارتن ");
            $table->float("number_product")->nullable()->comment("تعداد تولید");
            $table->float("sub_number_product")->nullable()->comment("تعداد تولید تکی"); 
               
            $table->string("production_speed")->nullable()->comment("سرعت تولید");

            $table->float("productivity_index")->nullable()->comment("شاخص عملکرد ");

            /****************************** Duplication Production */
            $table->integer("version")->nullable()->comment("خالی => بدون ورژن و 0=> اصلی ورژن دار و عدد => فرعی")->default;

            $table->integer("status_id")->nullable();


            /********************************* */

            $table->integer("supervisor_worker_id")->nullable();
            $table->integer("supervisor_worker_code")->nullable();


            $table->integer("prioriry_id")->nullable();
            $table->datetime("offer_start_date")->nullable();
            $table->datetime("offer_end_date")->nullable();

        
           
            $table->datetime("created_at")->default(DB::raw('CURRENT_TIMESTAMP'));
           
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tem_production_card');
    }
}
