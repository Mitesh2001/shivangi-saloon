<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->after('name')->nullable();
            $table->string('last_name')->after('first_name')->nullable(); 
            $table->string('nick_name')->after('last_name')->nullable();
            $table->string('branch_id')->after('language')->nullable();
            $table->string('expertise')->after('secondary_number')->nullable();
            // $table->string('role_id')->after('secondary_number')->nullable(); 
            $table->date('date_of_joining')->after('address')->nullable();
            $table->string('salary')->after('date_of_joining')->nullable();
            $table->string('basic')->after('salary')->nullable();
            $table->string('pf')->after('basic')->nullable();
            $table->string('gratutity')->after('pf')->nullable();
            $table->string('others')->after('gratutity')->nullable();
            $table->string('pt')->after('others')->nullable();
            $table->string('income_tax')->after('pt')->nullable();
            $table->string('over_time_ph')->after('income_tax')->nullable();
            $table->string('working_hours')->after('over_time_ph')->nullable();
            $table->string('total_experience')->after('working_hours')->nullable();

            $table->string('account_number')->after('total_experience')->nullable();
            $table->string('holder_name')->after('account_number')->nullable();
            $table->string('bank_name')->after('holder_name')->nullable();
            $table->string('isfc_code')->after('bank_name')->nullable();
            $table->string('bank_attachment')->after('isfc_code')->nullable();
            $table->string('profile_pic')->after('bank_attachment')->nullable(); 
 
            $table->json('week_off')->after('profile_pic')->nullable(); 
            $table->json('employeer')->after('week_off')->nullable(); 
            $table->json('certificates')->after('employeer')->nullable(); 

            $table->integer('created_by')->index('cerated_by')->default(0);
            $table->integer('updated_by')->index('updated_by')->default(0);
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
            $table->dropColumn('first_name');
            $table->dropColumn('last_name');
            $table->dropColumn('nick_name');
            $table->dropColumn('branch_id');
            $table->dropColumn('expertise');
            $table->dropColumn('date_of_joining');
            $table->dropColumn('salary');
            $table->dropColumn('basic');
            $table->dropColumn('pf'); 
            $table->dropColumn('gratutity');
            $table->dropColumn('others');
            $table->dropColumn('pt');
            $table->dropColumn('income_tax');
            $table->dropColumn('over_time_ph');
            $table->dropColumn('working_hours');
            $table->dropColumn('total_experience');
            $table->dropColumn('account_number');
            $table->dropColumn('holder_name');
            $table->dropColumn('bank_name');
            $table->dropColumn('isfc_code');
            $table->dropColumn('bank_attachment');
            $table->dropColumn('week_off');
            $table->dropColumn('employeer');
            $table->dropColumn('certificates');
            $table->dropColumn('profile_pic');
            $table->dropColumn('created_by');
            $table->dropColumn('updated_by');
        });
    }
}
