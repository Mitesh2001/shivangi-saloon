<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductIncomeEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_income_entries', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->nullable();

            $table->string('product_name')->nullable(); 
            $table->integer('product_id')->index('product_id')->default(0);
            $table->string('product_type')->nullable();
            $table->string('sku_code')->nullable();
            $table->string('mrp')->nullable();
            $table->integer('qty')->default(0);
            $table->string('cost_per_unit')->nullable();
            $table->integer('gst_percent')->default(0);
            $table->string('total_cost')->nullable();
            $table->date('expiry')->nullable();

            $table->integer('stock_income_history_id')->index('stock_income_history_id')->default(0);

            $table->integer('branch_id')->index('branch_id')->default(0);
            $table->integer('cerated_by')->index('cerated_by')->default(0);
            $table->integer('updated_by')->index('updated_by')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_income_entries');
    }
}
