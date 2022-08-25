<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->increments('id');
            $table->string('external_id');
            $table->string('client_name')->nullable();
            $table->string('contact_number',20)->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('enquiry_for')->nullable();
            $table->string('enquiry_type')->nullable();
            $table->text('enquiry_response')->nullable();
            $table->date('date_to_follow')->nullable();
            $table->string('enquiry_source')->nullable();
            $table->string('title');
            $table->text('description');
            $table->integer('status_id')->unsigned();
            $table->foreign('status_id')->references('id')->on('statuses');
            $table->integer('user_assigned_id')->unsigned();
            $table->foreign('user_assigned_id')->references('id')->on('users');
            $table->integer('client_id')->unsigned();
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->integer('user_created_id')->unsigned();
            $table->foreign('user_created_id')->references('id')->on('users');
            $table->datetime('deadline');
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
        Schema::drop('leads');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
