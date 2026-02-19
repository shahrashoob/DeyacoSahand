<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinancialSoftwareTransferFormTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('financial_software_transfer_forms', function (Blueprint $table) {

            $table->string("code");
            $table->foreignId("form_id");
            $table->foreignId("status_id");
            $table->foreignId("financial_software_id");
            $table->foreignId( "accounting_document_status_id" );
            $table->foreignId( "warehouse_transaction_status_id" );
            $table->foreignId( "sale_invoice_status_id" );
            $table->timestamps();
        });

        DB::statement("ALTER TABLE `financial_software_transfer_forms` comment 'فرم های انتقال به نرم افزار مالی'");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('financial_software_trans_kind_transfer_forms');
    }
}
