<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMainTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('main', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id');
            $table->string('api_username');
            $table->decimal('amount', 15, 2);
            $table->enum('winlost', ['win','lost']);
            $table->dateTime('timestamp')->nullable();
            $table->string('platform');
            $table->enum('status', ['successful'])->nullable();
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
        Schema::dropIfExists('main');
    }
}
