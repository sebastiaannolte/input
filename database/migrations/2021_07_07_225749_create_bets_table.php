<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bets', function (Blueprint $table) {
            $table->id();
            $table->integer('match_id')->nullable();
            $table->string('event')->nullable();
            $table->string('selection');
            $table->string('bookie');
            $table->double('stake');
            $table->double('odds');
            $table->string('tipster')->nullable();
            $table->string('sport');
            $table->string('type')->nullable();
            $table->timestamp('date');
            $table->string('result')->nullable();
            $table->string('status');
            $table->integer('user_id');
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
        Schema::dropIfExists('bets');
    }
}
