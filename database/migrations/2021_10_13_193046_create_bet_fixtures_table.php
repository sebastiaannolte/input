<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBetFixturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bet_fixtures', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('bet_id')->unsigned();
            $table->bigInteger('fixture_id')->nullable()->unsigned();
            $table->string('event');
            $table->string('selection');
            $table->bigInteger('category')->unsigned();
            $table->string('status');
            $table->timestamp('date');
            $table->timestamps();

            $table->foreign('bet_id')->references('id')->on('bets')->onDelete('cascade');
            $table->foreign('fixture_id')->references('id')->on('fixtures')->onDelete('cascade');
            $table->foreign('category')->references('id')->on('bet_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bet_fixtures');
    }
}
