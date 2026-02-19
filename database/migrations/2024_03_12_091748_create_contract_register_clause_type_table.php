<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateContractRegisterClauseTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contract_register_clause_type', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_register_id')->comment("قرارداد ثبت شده");
            $table->foreignId('clause_article_id')->comment("بند های قرارداد");
            $table->integer('priority_number')->nullable()->comment("اولویت");

            $table->timestamps();
        });
        DB::statement('ALTER TABLE contract_register_clause_type COMMENT "جدول مرتبط با بند های قرارداد ثبت شده"');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contract_register_clause_type');
    }
}
