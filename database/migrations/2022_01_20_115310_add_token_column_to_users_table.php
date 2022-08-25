<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTokenColumnToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('token')->nullable()->after('plan_commission');
            $table->integer('no_of_logins')->default(0)->after('token');
            $table->integer('no_of_login_attempts')->unsigned()->nullable()->default(0)->after('no_of_logins');
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
            $table->dropColumn('token');
            $table->dropColumn('no_of_logins');
            $table->dropColumn('no_of_login_attempts');
        });
    }
}
