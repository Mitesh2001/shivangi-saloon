<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductServiceCommissionToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('commission');

            $table->integer('product_commission')->default(0)->comment("product commission in percentage"); 
            $table->integer('service_commission')->default(0)->comment("service commission in percentage"); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) { 
            $table->integer('commission')->default(0)->comment("Commission in percentage"); 

            $table->dropColumn('product_commission'); 
            $table->dropColumn('service_commission'); 
        });
    }
}
