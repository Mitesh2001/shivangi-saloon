<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plans', function (Blueprint $table) {
            // $table->dropColumn('validity');
            // $table->dropColumn('number_of_employees');
            $table->string('description')->nullable()->after('price');
            $table->integer('duration_months')->default(0)->after('description');
            $table->integer('no_of_users')->default(0)->after('duration_months');
            $table->integer('no_of_branches')->default(0)->after('no_of_users');
            $table->integer('no_of_sms')->default(0)->after('no_of_branches');
            $table->integer('no_of_email')->default(0)->after('no_of_sms');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->integer('validity')->default(0)->comment('Validity in days'); 
            $table->dropColumn('description');
            $table->dropColumn('duration_months');
            $table->dropColumn('no_of_users');
            $table->dropColumn('no_of_branches');
            $table->dropColumn('no_of_sms');
            $table->dropColumn('no_of_email');
        });
    }
}
