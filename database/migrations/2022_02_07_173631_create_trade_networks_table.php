<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTradeNetworksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trade_networks', function (Blueprint $table) {
            $table->id();
            $table->boolean('status')->default(true);
            $table->string('name', 255);
            $table->string('url', 255)->nullable();
            $table->string('title', 255);
            $table->string('sub_title', 255)->nullable();
            $table->foreignId('type_promocode_id')->references('id')->on('type_promocodes');
            $table->string('instruction_title', 400)->nullable();
            $table->jsonb('instruction_questions')->nullable();
            $table->boolean('show_instruction')->default(true);
            $table->foreignId('product_id')->references('id')->on('products');
            $table->boolean('quiz_show')->default(false);
            $table->boolean('quiz_own_answer')->default(false);
            $table->string('quiz_type_answers', 255)->nullable();
            $table->string('quiz_question', 255)->nullable();
            $table->jsonb('quiz_answers')->nullable();
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
        Schema::dropIfExists('trade_networks');
    }
}
