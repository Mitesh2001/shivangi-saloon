<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockEditHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_edit_history', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->nullable();  
            $table->integer('product_id')->default(0);
            $table->string('invoice_number')->nullable();

            $table->integer('old_qty')->default(0);
            $table->integer('new_qty')->default(0);
            $table->string('old_cost_per_unit')->nullable();
            $table->string('new_cost_per_unit')->nullable(); 
            $table->string('old_gst_percent')->nullable();
            $table->string('new_gst_percent')->nullable(); 
            $table->string('old_mrp')->nullable();
            $table->string('new_mrp')->nullable(); 
            $table->string('remarks')->nullable();
            $table->date('date')->nullable();

            $table->integer('branch_id')->index('branch_id')->default(0);;
            $table->integer('cerated_by')->index('cerated_by')->default(0);;
            $table->integer('updated_by')->index('updated_by')->default(0);;
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
        Schema::dropIfExists('stock_edit_history');
    }
}
