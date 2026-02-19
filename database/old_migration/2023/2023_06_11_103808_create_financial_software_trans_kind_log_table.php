<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinancialSoftwareTransKindLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('financial_software_trans_kind_log', function (Blueprint $table) {

            $table->id();
            $table->foreignId("form_id");
            $table->foreignId("financial_software_id");
            $table->foreignId("accounting_document_status_id");
            $table->foreignId("warehouse_transaction_status_id");
            $table->foreignId("sale_invoice_status_id");

            $table->longText("accounting_document_result");
            $table->longText("warehouse_transaction_result");
            $table->longText("sale_invoice_result");

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
        Schema::dropIfExists('finance_software_trans_kind_log');
    }
}
