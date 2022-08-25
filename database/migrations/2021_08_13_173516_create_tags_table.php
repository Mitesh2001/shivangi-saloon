<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('external_id');
            $table->string('name')->nullable();
            $table->string('type')->nullable();
            $table->integer('created_by')->index('created_by'); 
            $table->integer('updated_by')->index('updated_by'); 
            $table->integer('is_archive')->default(0)->comment("0 = not archived, 1 = archived");
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
        Schema::dropIfExists('tags');
    }
}
