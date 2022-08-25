<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients_product', function (Blueprint $table) {
            $table->id();
            $table->string('external_id');

            $table->bigInteger('order_id')->index('order_id')->default(0);
            $table->bigInteger('client_id')->index('client_id')->default(0);
            $table->bigInteger('product_id')->index('product_id')->default(0);
            $table->float('product_price')->default(0);
            $table->float('discount')->default(0);
            $table->float('discount_amount')->default(0);
            $table->float('final_amount')->default(0);
            $table->date('order_date')->nullable();
  
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
        Schema::dropIfExists('clients_product');
    }
}
