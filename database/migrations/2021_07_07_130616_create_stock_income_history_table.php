<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockIncomeHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_income_history', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->nullable();; 

            $table->string('invoice_number')->nullable();;
            $table->date('date')->nullable();
            $table->string('invoice_value')->nullable();
            $table->string('extra_freight_charges')->nullable();
            $table->string('source_type')->nullable();
            $table->integer('source_id')->index('source_id')->default(0);
            $table->string('invoice_type')->nullable();

            $table->string('notes')->nullable();
            $table->string('amount_paid')->nullable();
            $table->string('payment_type')->nullable();
            $table->string('payment_status')->nullable();
            
            $table->json('products_array')->nullable();

            $table->integer('vendor_id')->index('vendor_id')->default(0);
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
        Schema::dropIfExists('stock_income_history');
    }
}
