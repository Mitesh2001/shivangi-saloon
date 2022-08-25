<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSubscriptionFieldsToUsersCommissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_commission', function (Blueprint $table) {
            // $table->dropColumn('user_commission');
            $table->integer('user_type')->default(0)->after('user_id');
            $table->integer('subscription_id')->index('subscription_id')->default(0)->after('user_type');
            $table->float('user_subscription_commission')->default(0)->after('user_service_commission');
            $table->float('subscription_commission')->default(0)->after('service_commission'); 
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
            $table->dropColumn('user_type');
            $table->dropColumn('subscription_id');
            $table->dropColumn('user_subscription_commission');
            $table->dropColumn('subscription_commission'); 
        });
    }
}
