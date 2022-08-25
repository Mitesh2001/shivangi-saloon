<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDealsAndDiscountsProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deals_and_discounts_products', function (Blueprint $table) {
            $table->id();
            $table->string('external_id');
            $table->integer('product_type')->index('product_type')->default(0)->comment('0 = product, 1 = service');
            $table->integer('category_id')->index('category_id')->default(0);
            $table->integer('sub_category_id')->index('sub_category_id')->default(0);
            $table->integer('product_id')->index('product_id')->default(0);
            $table->string('product_min_price')->nullable();
            $table->string('product_max_price')->nullable();
            $table->integer('deal_id')->index('deal_id')->default(0);  
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
        Schema::dropIfExists('deals_and_discounts_products');
    }
}
