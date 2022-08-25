<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStateNameToClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('event_type')->nullable()->before('content');
            $table->string('before_days')->nullable()->comment('Send sms before number of days')->before('event_type');
            $table->dateTime('event_date')->nullable()->comment('Send sms on selected date')->before('before_days'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('event_type'); 
            $table->dropColumn('before_days'); 
            $table->dropColumn('event_date'); 
        });
    }
}
