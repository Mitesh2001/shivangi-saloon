<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionsTableForSalons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('subscription_id')->nullable();
            $table->integer('salon_id')->index('salon_id')->default(0); // Salon Id (Distributor table) module renamed as salon 
            $table->float('discount')->default(0);
            $table->float('discount_amount')->default(0);
            $table->float('sgst')->default(0);
            $table->float('cgst')->default(0);
            $table->float('igst')->default(0);
            $table->float('sgst_amount')->default(0);
            $table->float('cgst_amount')->default(0);
            $table->float('igst_amount')->default(0);
            $table->float('final_amount')->default(0);
            $table->string('payment_mode')->nullable(); 
            $table->string('payment_bank_name')->nullable(); 
            $table->string('payment_number')->nullable(); 
            $table->float('total_amount')->default(0);
            $table->float('payment_amount')->default(0); 
            $table->integer('state_id')->default(0); 
            $table->date('payment_date')->nullable(); 
            $table->date('subscription_expiry_date')->nullable(); 
            $table->integer('created_by')->index('created_by')->default(0); 
            $table->integer('updated_by')->index('updated_by')->default(0); 
            $table->integer('deleted_by')->index('deleted_by')->default(0); 
            $table->string('is_payment_pending')->nullable(); 
            $table->integer('round_off_amount')->default(0); 
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
        Schema::dropIfExists('subscriptions');
    }
}
