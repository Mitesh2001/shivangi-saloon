<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_settings', function (Blueprint $table) {
            $table->id();
            $table->string('api_url')->nullable();
            $table->json('parameters')->nullable(); 
            $table->string('final_url')->nullable(); 
            $table->bigInteger('updated_by')->defualt(0)->nullable();
            $table->string('mobile_param')->nullable();
            $table->string('msg_param')->nullable();
            $table->integer('is_tested')->default(0);
            $table->integer('is_working')->default(0);
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
        Schema::dropIfExists('sms_settings');
    }
}
