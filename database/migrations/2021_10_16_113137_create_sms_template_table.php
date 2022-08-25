<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsTemplateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_template', function (Blueprint $table) { 
            $table->string('external_id');
            $table->string('name')->nullable(); 
            $table->text('message')->nullable(); 
            $table->tinyInteger('default_template')->default(0);
            $table->string('event_type')->nullable();
            $table->string('before_days')->nullable()->comment('Send sms before number of days');
            $table->dateTime('event_date')->nullable()->comment('Send sms on selected date'); 
			$table->integer('client_id')->default(0);
			$table->integer('created_by')->default(0);
            $table->integer('updated_by')->default(0);  
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
        Schema::dropIfExists('sms_template');
    }
}
