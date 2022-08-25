<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductServiceCommissionToUsersCommissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_commission', function (Blueprint $table) {
            $table->integer('user_product_commission')->after('order_id'); 
            $table->integer('user_service_commission')->after('user_product_commission');
            $table->integer('service_commission')->default(0)->after('product_commission');    
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
            $table->dropColumn('user_product_commission'); 
            $table->dropColumn('user_service_commission');
            $table->dropColumn('service_commission');
        });
    }
}
