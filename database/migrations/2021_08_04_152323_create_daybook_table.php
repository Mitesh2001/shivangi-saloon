<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDaybookTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()    
    {
        Schema::create('daybook', function (Blueprint $table) {
            $table->id();

            $table->string('external_id')->nullable();
            $table->string('amount')->nullable();

            $table->integer('entry_type')->default(0)->comment('0 = cash in, 1 = cash out');
            $table->string('payment_method')->nullable()->comment('method of cashout');
            $table->integer('branch_id')->index('branch_id')->default(0);
            $table->date('date')->nullable();
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
        Schema::dropIfExists('daybook');
    }
}
