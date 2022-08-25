<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->nullable();  

            $table->string('name')->nullable();  
            $table->string('gst_number')->nullable();  
            $table->string('primary_number')->nullable();  
            $table->string('secondary_number')->nullable();  
            $table->string('primary_email')->nullable();  
            $table->string('secondary_email')->nullable();  
            $table->string('contact_person')->nullable();  
            $table->string('contact_person_number')->nullable();  
            $table->string('contact_person_email')->nullable();  
            $table->string('city')->nullable();  
            $table->string('address')->nullable();  
            $table->string('zipcode')->nullable();  

            $table->integer('created_by')->index('cerated_by')->default(0);
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
        Schema::dropIfExists('vendors');
    }
}
