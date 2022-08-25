<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDistributorIdToLogsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sms_log', function (Blueprint $table) {
            $table->integer('distributor_id')->default(0);
        });
        Schema::table('email_log', function (Blueprint $table) {
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
        Schema::table('sms_log', function (Blueprint $table) {
            $table->dropColumn('distributor_id');
        });
        Schema::table('email_log', function (Blueprint $table) {
            $table->dropColumn('distributor_id');
        });
    }
}
