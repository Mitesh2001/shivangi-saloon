<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToDistributorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('distributors', function (Blueprint $table) {
            $table->string('pan_number')->nullable();  
            $table->integer('number_of_employees')->default(0);  
            $table->integer('country_id')->index('country_id')->default(0);  
            $table->integer('state_id')->index('state_id')->default(0);  
            $table->string('logo')->nullable();  
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
            $table->dropColumn('pan_number'); 
            $table->dropColumn('number_of_employees');  
            $table->dropColumn('country_id'); 
            $table->dropColumn('state_id');  
            $table->dropColumn('logo');
        });
    }
}
