<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldOnDealsAndDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('deals_and_discounts', function (Blueprint $table) { 
            $table->integer('apply_on_bill_total')->default(0); 
        });
        Schema::dropIfExists('deals_and_discounts_products');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('deals_and_discounts', function (Blueprint $table) {
            $table->dropColumn('apply_on_bill_total');
        }); 
    }
}
