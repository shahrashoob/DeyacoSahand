<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string("code");
            $table->string("caption");
            $table->foreignId("ticket_type_id")->constrained();
            $table->foreignId("user_id")->constrained();
            $table->foreignId("status_id")->references("id")->on("status");
            $table->foreignId("machine_id")->nullable();
            $table->foreignId("ic")->nullable()->references("id")->on("cost_centers");
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tickets');
    }
}
