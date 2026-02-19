<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractClauseTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contract_clause_type', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->comment("قرارداد");
            $table->foreignId('clause_type_id')->comment("ماده های قرارداد");
            $table->foreignId('clause_article_id')->nullable()->comment("بنذهای قرارداد");
            $table->integer('priority_number')->nullable()->comment("اولویت");
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
        Schema::dropIfExists('contract_clause_type');
    }
}
