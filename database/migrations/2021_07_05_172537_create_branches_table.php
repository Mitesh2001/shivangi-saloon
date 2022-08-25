<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('external_id');
            $table->string('name');
            $table->integer('primary_contact_person')->index('primary_contact_person')->nullable();
            $table->integer('secondary_contact_person')->index('secondary_contact_person')->nullable();
            $table->string('primary_contact_number');
            $table->string('secondary_contact_number')->nullable();
            $table->string('primary_email')->nullable();
            $table->string('secondary_email')->nullable();
            $table->string('city')->nullable();
            $table->string('address')->nullable();
            $table->string('zipcode')->nullable();
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
        Schema::dropIfExists('branches');
    }
}
