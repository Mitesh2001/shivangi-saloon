<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockMasterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_master', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->nullable();;
            $table->integer('product_id')->index('product_id')->default(0);
            
            $table->integer('qty')->default(0); 
            $table->integer('branch_id')->index('branch_id')->default(0);

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
        Schema::dropIfExists('stock_master');
    }
}
