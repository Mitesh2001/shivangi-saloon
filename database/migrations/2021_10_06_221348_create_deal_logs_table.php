<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDealLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deal_logs', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->nullable();

            $table->integer('order_id')->default(0)->index('order_id');
            $table->integer('deal_id')->default(0)->index('deal_id');
            $table->json('deal_json')->nullable();

            $table->softDeletes();
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
        Schema::dropIfExists('deal_logs');
    }
}
