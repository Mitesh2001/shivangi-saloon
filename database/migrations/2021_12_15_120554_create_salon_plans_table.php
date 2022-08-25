<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalonPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salon_plans', function (Blueprint $table) {
            $table->id();
            $table->integer('salon_id')->index('salon_id')->default(0)->comment('salon_id = distributors.id');
            $table->integer('subscription_id')->index('subscription_id')->default(0);
            $table->integer('plan_id')->index('plan_id')->default(0);
            $table->integer('no_of_sms')->default(0);
            $table->integer('no_of_email')->default(0);
            $table->integer('no_of_users')->default(0);
            $table->integer('no_of_branches')->default(0);
            $table->integer('duration_months')->default(0); 
            $table->float('plan_price')->default(0);
            $table->float('discount')->default(0);
            $table->float('discount_amount')->default(0);
            $table->float('sgst')->default(0);
            $table->float('sgst_amount')->default(0); 
            $table->float('cgst')->default(0);
            $table->float('cgst_amount')->default(0); 
            $table->float('igst')->default(0);
            $table->float('igst_amount')->default(0); 
            $table->float('final_amount')->default(0); 
            $table->date('subscription_date')->nullable();
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
        Schema::dropIfExists('salon_plans');
    }
}
