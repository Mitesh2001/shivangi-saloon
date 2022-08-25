<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmailSmsServicesToDistributorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('distributors', function (Blueprint $table) {
            $table->string('sender_id')->nullable()->after('zipcode');
            $table->string('from_email')->nullable()->after('sender_id');
            $table->string('from_name')->nullable()->after('from_email');
            $table->integer('sms_service')->default(0)->after('from_name')->comment('0 = inactive, 1 = active');
            $table->integer('email_service')->default(0)->after('sms_service')->comment('0 = inactive, 1 = active');
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
            $table->dropColumn('sender_id');
            $table->dropColumn('from_email');
            $table->dropColumn('from_name');
            $table->dropColumn('sms_service');
            $table->dropColumn('email_service');
        });
    }
}
