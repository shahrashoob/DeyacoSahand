<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinancialSoftwareTransKindFormTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('financial_software_trans_kind_form', function (Blueprint $table) {
            $table->id();
            $table->foreignId("form_id");
            $table->foreignId("financial_software_id");
            $table->foreignId("accounting_document_status_id");
            $table->foreignId("warehouse_transaction_status_id");
            $table->foreignId("sale_invoice_status_id");
            $table->timestamps();
        });

        DB::statement("ALTER TABLE `financial_software_trans_kind_form` comment 'به ازای هر نوع تراکنش حسابداری(تراکنش انبار، فروش ، حسابداری و ...) نتیجه ثبت تراکنش ها در این جدول نمایش داده می شود.'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('financial_software_trans_kind_form');
    }
}
