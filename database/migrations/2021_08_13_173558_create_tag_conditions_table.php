<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTagConditionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tag_conditions', function (Blueprint $table) {
            $table->id();
            $table->integer('tag_id')->index('tag_id')->default(0);
            $table->string('kpi')->nullable();
            $table->integer('start_range')->default(0);
            $table->integer('end_range')->default(0);
            $table->date('date_start_range')->nullable();
            $table->date('date_end_range')->nullable();
            $table->date('date_last_visit')->nullable();
            $table->integer('expiry_days_remain')->default(0);
            $table->integer('avg_orders')->default(0);
            $table->integer('gender')->nullable()->comment('0 = male, 1 = female');
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
        Schema::dropIfExists('tag_conditions');
    }
}
