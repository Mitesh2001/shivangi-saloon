<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGstFieldsToClientsProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients_product', function (Blueprint $table) {
            $table->float('igst')->default(0)->after('final_amount');
            $table->float('sgst')->default(0)->after('igst');
            $table->float('cgst')->default(0)->after('sgst');  
            $table->float('igst_amount')->default(0)->after('cgst');
            $table->float('sgst_amount')->default(0)->after('igst_amount');
            $table->float('cgst_amount')->default(0)->after('sgst_amount');  
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clients_product', function (Blueprint $table) {
            $table->dropColumn('igst');
            $table->dropColumn('sgst');
            $table->dropColumn('cgst');
            $table->dropColumn('igst_amount');
            $table->dropColumn('sgst_amount');
            $table->dropColumn('cgst_amount');
        });
    }
}
