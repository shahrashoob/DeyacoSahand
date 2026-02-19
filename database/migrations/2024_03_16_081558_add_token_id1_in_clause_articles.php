<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTokenId1InClauseArticles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clause_articles', function (Blueprint $table) {
            $table->foreignId('token_id1')->nullable();
            $table->foreignId('token_id2')->nullable();
            $table->foreignId('token_id3')->nullable();
            $table->foreignId('token_id4')->nullable();
            $table->foreignId('token_id5')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clause_articles', function (Blueprint $table) {
            //
        });
    }
}
