<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeDatatypeOfUsersCommissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_commission', function (Blueprint $table) {
            $table->float('user_product_commission')->after('order_id')->change(); 
            $table->float('user_service_commission')->after('user_product_commission')->change(); 
            $table->float('service_commission')->default(0)->after('product_commission')->change();  
            $table->float('invoice_commission')->default(0)->change(); 
            $table->float('product_commission')->default(0)->change(); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users_commission', function (Blueprint $table) {
            $table->integer('user_product_commission')->after('order_id')->change(); 
            $table->integer('user_service_commission')->after('user_product_commission')->change(); 
            $table->integer('service_commission')->default(0)->after('product_commission')->change();  
            $table->integer('invoice_commission')->default(0)->change(); 
            $table->integer('product_commission')->default(0)->change(); 
        });
    }
}
