<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_log', function (Blueprint $table) {
            $table->id();
            $table->integer('template_id')->default(0);
            $table->integer('client_id')->default(0);
            $table->string('sender_id')->default(0);
            $table->integer('number_of_sms')->default(0)->comment("Number of SMS Credit deducted");
            $table->string('number')->nullable();
            $table->string('message_body')->nullable();
            $table->string('event_type')->nullable(); 
            $table->json('template_json')->default(0);
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
        Schema::dropIfExists('sms_log');
    }
}
