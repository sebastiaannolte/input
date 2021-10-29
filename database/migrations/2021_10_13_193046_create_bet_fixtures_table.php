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
            $table->integer('bet_id');
            $table->integer('fixture_id')->nullable();
            $table->string('event');
            $table->string('selection');
            $table->string('category');
            $table->string('status');
            $table->timestamp('date');
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
        Schema::dropIfExists('bet_fixtures');
    }
}
