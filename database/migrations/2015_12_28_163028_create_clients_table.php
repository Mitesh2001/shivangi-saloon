<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->increments('id');
            $table->string('external_id');
            $table->string('name')->nullable();
            $table->string('address')->nullable();
            $table->string('zipcode')->nullable();
            $table->string('city')->nullable();
            $table->string('company_name')->nullable();
            $table->string('vat')->nullable();
            $table->string('company_type')->nullable();
            $table->enum('gender',['Male', 'Female'])->nullable();
            $table->bigInteger('client_number')->nullable();
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->integer('industry_id')->unsigned()->nullable();
            $table->foreign('industry_id')->references('id')->on('industries')->nullable();
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
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::drop('clients');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
