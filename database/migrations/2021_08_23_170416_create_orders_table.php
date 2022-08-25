<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) { 
            $table->bigIncrements('id');
            $table->string('subscriptions_uid',100)->default(0)->nullable();
            $table->string('external_id')->nullable();
			$table->unsignedBigInteger('client_id')->default(0);
			$table->unsignedBigInteger('branch_id')->default(0);
			$table->float('total_amount',10, 2)->default(0);
			$table->float('discount',4, 2)->default(0);
			$table->float('discount_amount',10, 2)->default(0);
			$table->float('sgst',4, 2)->default(0);
			$table->float('cgst',4, 2)->default(0);
			$table->float('igst',4, 2)->default(0);
			$table->float('sgst_amount',10, 2)->default(0);
			$table->float('cgst_amount',10, 2)->default(0);
			$table->float('igst_amount',10, 2)->default(0);
			$table->float('final_amount',10, 2)->default(0);
			$table->string('payment_mode',10)->nullable()->comment('Payment Mode');
			$table->string('payment_bank_name',50)->nullable()->comment('Payment Bank Name');
			$table->string('payment_number',20)->nullable()->comment('Payment Transction or check number');
			$table->string('payment_amount',20)->nullable()->comment('Payment Amount');
			$table->date('payment_date',20)->nullable()->comment('Payment Date');
			$table->bigInteger('created_by')->unsigned()->default(0);
            $table->bigInteger('updated_by')->unsigned()->default(0);
            $table->bigInteger('deleted_by')->unsigned()->default(0);
            $table->index(['created_by','updated_by','deleted_by']); 
			$table->index(['client_id', 'branch_id']);
            $table->string('is_payment_pending',10)->nullable();
            $table->float('round_off_amount',10, 2)->default(0); 
            $table->timestamps();
            $table->softDeletes(); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
