<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGstFieldsToPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->float('sgst')->default(0)->after('price'); 
            $table->float('cgst')->default(0)->after('sgst'); 
            $table->float('igst')->default(0)->after('cgst'); 
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
            $table->dropColumn('sgst'); 
            $table->dropColumn('cgst'); 
            $table->dropColumn('igst'); 
        });
    }
}
