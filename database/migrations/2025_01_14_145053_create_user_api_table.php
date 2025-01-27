<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserApiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_api', function (Blueprint $table) {
            $table->id();
            $table->string('merchant');
            $table->string('product_id');
            $table->string('wallet');
            $table->string('user_currency');
            $table->bigInteger('player_id');
            $table->string('player_name');
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
        Schema::dropIfExists('user_api');
    }
}
