<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppointmentImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appointment_images', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->nullable();
            $table->string('image')->nullable();
            $table->integer('appointment_id')->index('appointment_id')->default(0);
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
        Schema::dropIfExists('appointment_images');
    }
}
