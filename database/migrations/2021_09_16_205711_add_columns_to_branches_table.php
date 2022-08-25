<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToBranchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->integer('country_id')->index('country_id')->default(0)->after('secondary_email');  
            $table->integer('state_id')->index('state_id')->default(0)->after('country_id');  
            $table->string('state_name')->nullable()->after('state_id');   
            $table->integer('is_primary')->default(0)->after('zipcode');   
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn('country_id'); 
            $table->dropColumn('state_id');  
            $table->dropColumn('state_name');  
            $table->dropColumn('is_primary');  
        });
    }
}
