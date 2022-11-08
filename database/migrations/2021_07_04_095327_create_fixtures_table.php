<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFixturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fixtures', function (Blueprint $table) {
            $table->id();
            $table->integer('fixture_id');
            $table->bigInteger('home_team')->unsigned();
            $table->bigInteger('away_team')->unsigned();
            $table->string('referee')->nullable();
            $table->string('sport');
            $table->bigInteger('league_id')->unsigned();
            $table->bigInteger('venue_id')->nullable()->unsigned();
            $table->string('timezone');
            $table->string('date');
            $table->timestamps();

            $table->foreign('league_id')->references('id')->on('leagues')->onDelete('cascade');
            $table->foreign('venue_id')->references('id')->on('venues')->onDelete('cascade');
            $table->foreign('home_team')->references('id')->on('teams')->onDelete('cascade');
            $table->foreign('away_team')->references('id')->on('teams')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fixtures');
    }
}
