<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromocodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promocodes', function (Blueprint $table) {
            $table->id();
            $table->string('code', 256)->unique();
            $table->foreignId('trade_network_id')->references('id')->on('trade_networks');
            $table->foreignId('product_id')->references('id')->on('products');
            $table->boolean('free')->default(true);
            $table->timestamps();
            $table->index(['code', 'trade_network_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('promocodes');
    }
}
