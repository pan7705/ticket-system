<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsTable extends Migration
{
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->integer('ticket_number');
            $table->enum('status', ['waiting', 'serving', 'completed'])->default('waiting');
            $table->unsignedBigInteger('counter_id')->nullable();
            $table->timestamps();

            $table->foreign('counter_id')->references('id')->on('counters');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tickets');
    }
}
