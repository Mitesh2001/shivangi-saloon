<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnquiriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('enquiries', function (Blueprint $table) {
            $table->id();
            $table->string('external_id');
            $table->integer('client_id')->index('client_id'); 
            $table->string('client_name');
            $table->string('contact_number');
            $table->string('email');
            $table->string('address')->nullable();
            $table->string('description')->nullable();
            $table->string('enquiry_for');
            $table->integer('enquiry_type')->index('enquiry_type'); 
            $table->string('enquiry_response');
            $table->dateTime('date_to_follow');
            $table->string('source_of_enquiry');
            $table->string('user_assigned_id'); 
            $table->integer('status_id')->index('status_id');
            $table->integer('created_by')->index('created_by'); 
            $table->integer('updated_by')->index('updated_by'); 
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
        Schema::dropIfExists('enquiries');
    }
}
