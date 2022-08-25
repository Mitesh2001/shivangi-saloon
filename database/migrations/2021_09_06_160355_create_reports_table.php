<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('external_id');
            $table->string('name')->nullable();
            $table->string('module')->nullable();
            $table->string('group_by')->nullable();
            $table->string('group_by_two')->nullable();
            $table->json('select_columns')->nullable();

            $table->text('rules_query')->nullable();
            $table->json('rules_set')->nullable();

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
        Schema::dropIfExists('reports');
    }
}
