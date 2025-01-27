<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log', function (Blueprint $table) {
            $table->id();
            $table->string('bet_id')->index();
            $table->string('player_name');
            $table->string('provider')->index();
            $table->string('game_id')->index();
            $table->string('currency', 3);
            $table->string('wallet');
            $table->decimal('bet_amount', 10, 4);
            $table->decimal('winloss_amount', 10, 4);
            $table->enum('status',['win','loss','draw']);
            $table->integer('processed')->default(0);
            $table->timestamp('bet_date')->nullable();
            $table->json('data')->nullable();
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
        Schema::dropIfExists('log');
    }
}
