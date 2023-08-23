<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromocodeLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promocode_logs', function (Blueprint $table) {
            $table->id();
            $table->string('promocode', 32);
            $table->string('trade_network', 255);
            $table->foreignId('user_id')->references('id')->on('users');
            $table->integer('mindbox_id');
            $table->foreignId('product_id')->references('id')->on('products');
            $table->foreignId('type_promocode_id')->references('id')->on('type_promocodes');
            $table->string('url', 255)->nullable();
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
        Schema::dropIfExists('promocode_logs');
    }
}
