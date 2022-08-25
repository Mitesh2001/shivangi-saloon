<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSubscriptionColumnsToDistributorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('distributors', function (Blueprint $table) {
            $table->integer('no_of_users')->default(0)->after('pan_number');
            $table->integer('no_of_branches')->default(0)->after('no_of_users');
            $table->integer('total_email')->default(0)->after('no_of_branches');
            $table->integer('used_email')->default(0)->after('total_email');
            $table->integer('total_sms')->default(0)->after('used_email');
            $table->integer('used_sms')->default(0)->after('total_sms');
            $table->date('expiry_date')->nullable()->after('used_sms');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('distributors', function (Blueprint $table) {
            $table->dropColumn('no_of_users');
            $table->dropColumn('no_of_branches');
            $table->dropColumn('total_email');
            $table->dropColumn('used_email');
            $table->dropColumn('total_sms');
            $table->dropColumn('used_sms');
            $table->dropColumn('expiry_date');
        });
    }
}
