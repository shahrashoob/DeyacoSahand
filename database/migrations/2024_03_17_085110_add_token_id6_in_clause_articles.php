<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTokenId6InClauseArticles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clause_articles', function (Blueprint $table) {
            $table->foreignId('token_id6')->nullable();
            $table->foreignId('token_id7')->nullable();
            $table->foreignId('token_id8')->nullable();
            $table->foreignId('token_id9')->nullable();
            $table->foreignId('token_id10')->nullable();
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
