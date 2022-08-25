<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDealDiscountToClientsProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients_product', function (Blueprint $table) {
            $table->integer('deal_discount')->default(0); 
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->string('discount_code')->nullable(); 
            $table->integer('deal_id')->index('deal_id')->default(0); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clients_product', function (Blueprint $table) {
            
            $table->dropColumn('deal_discount');
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('discount_code');
            $table->dropColumn('deal_id');
        });
    }
}
