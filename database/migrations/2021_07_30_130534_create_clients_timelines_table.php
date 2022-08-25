<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTimelinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients_timelines', function (Blueprint $table) {
            $table->id(); 

            $table->string('name')->nullable();
            $table->integer('from')->default(0);
            $table->integer('to')->default(0);
            $table->integer('other')->default(0);

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
        Schema::dropIfExists('clients_timelines');
    }
}
