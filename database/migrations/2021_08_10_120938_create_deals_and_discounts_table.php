<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDealsAndDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deals_and_discounts', function (Blueprint $table) {
            $table->id();
            $table->string('external_id');

            $table->string('customer_segment_client')->index('customer_segment_client')->nullable();  // from dynamimc customer category
            $table->integer('customer_segment_special')->index('customer_segment_special')->default(0);; // from customers segament table

            $table->string('deal_name')->nullable();
            $table->string('deal_code')->nullable();
            $table->string('deal_description')->nullable();
            
            $table->date('validity')->nullable();
            $table->datetime('start_at')->nullable();
            $table->datetime('end_at')->nullable();

            $table->integer('applicable_on_weekends')->default(0)->comment('0 = not applicable, 1 = applicable'); 
            $table->integer('applicable_on_holidays')->default(0)->comment('0 = not applicable, 1 = applicable');
            $table->integer('applicable_on_bday_anniv')->default(0)->comment('0 = not applicable, 1 = applicable');
            $table->string('week_days')->nullable(); 
 
            $table->string('invoice_min_amount')->nullable();
            $table->string('invoice_max_amount')->nullable();
            
            $table->string('redemptions_max')->nullable(); // Redemptions per client

            $table->string('benifit_type')->nullable(); // discount, cashback, loyalty points
            $table->string('discount')->nullable(); // number of dicount, cashback, points

            $table->json('products_service_array')->nullable();
            
            $table->integer('is_archive')->default(0)->comment('0 = not active, 1 = active');
            $table->integer('is_active')->default(0)->comment('0 = not active, 1 = active');
            $table->integer('created_by')->index('created_by')->default(0); 
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
        Schema::dropIfExists('deals_and_discounts');
    }
}
