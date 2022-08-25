<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersCommisionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_commission', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->nullable();

            $table->integer('user_id')->default(0)->index('user_id');
            $table->integer('order_id')->default(0)->index('order_id');
            $table->integer('user_commission')->default(0);
            $table->json('invoice_json')->nullable();
            $table->integer('invoice_commission')->default(0);
            $table->integer('product_commission')->default(0);
            $table->tinyInteger('is_paid')->default(0)->comment('0 = unpaid, 1 = paid');
   
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
        Schema::dropIfExists('users_commision');
    }
}
