<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersProductCommissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_product_commission', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id')->default(0)->index('product_id');
            $table->integer('user_id')->default(0)->index('user_id');
            $table->float('commission')->default(0)->comment('commission in %');
            $table->integer('created_by')->default(0)->index('created_by');
            $table->integer('updated_by')->default(0)->index('updated_by');
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
        Schema::dropIfExists('users_product_commission');
    }
}
