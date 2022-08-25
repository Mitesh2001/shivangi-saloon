<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGenderFieldToEnquiriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('enquiries', function (Blueprint $table) {
            $table->string('gender')->nullabel()->after('stage');
        });
        Schema::table('appointments', function (Blueprint $table) {
            $table->string('gender')->nullabel()->after('date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('enquiries', function (Blueprint $table) {
            $table->dropColumn('gender');
        });
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn('gender');
        });
    }
}
