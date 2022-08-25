<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGstFieldsToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->float('igst')->default(0)->after('thumbnail');
            $table->float('sgst')->default(0)->after('igst');
            $table->float('cgst')->default(0)->after('sgst');  
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('igst');
            $table->dropColumn('sgst');
            $table->dropColumn('cgst');  
        });
    }
}
