<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDistributorIdToUsersCommissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_commission', function (Blueprint $table) {
            $table->integer('distributor_id')->default(0); 
        });
        Schema::table('commission_release', function (Blueprint $table) {
            $table->integer('distributor_id')->default(0); 
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
            $table->dropColumn('distributor_id')->default(0); 
        });
        Schema::table('commission_release', function (Blueprint $table) {
            $table->dropColumn('distributor_id')->default(0); 
        });
    }
}
