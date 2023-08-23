<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIdxLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('idx_logs', function (Blueprint $table) {
            $table->id();
            $table->string('method',255);
            $table->string('phone',16);
            $table->text('params')->nullable();
            $table->string('result_code',10)->nullable();
            $table->string('result_code_text',255)->nullable();
            $table->string('score',10)->nullable();
            $table->string('score_text',255)->nullable();
            $table->text('response')->nullable();
            $table->double('duration')->nullable();
            $table->string('url', 255)->nullable();
            $table->string('trade_network', 255)->nullable();
            $table->string('product', 255)->nullable();
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
        Schema::dropIfExists('idx_logs');
    }
}
