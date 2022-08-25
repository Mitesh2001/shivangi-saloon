<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_templates', function (Blueprint $table) {
            $table->increments('email_template_id');
            $table->string('external_id');
            $table->string('name')->nullable();
            $table->string('subject')->nullable();
            $table->text('content')->nullable();
            $table->unsignedBigInteger('client_id')->default(0);
            $table->bigInteger('company_id')->unsigned()->default(0);
            $table->index(['company_id']);
            $table->tinyInteger('default_template')->nullable()->default(0);
			$table->bigInteger('createdBy')->nullable()->default(0);
            $table->bigInteger('updatedBy')->nullable()->default(0); 
			$table->index(['client_id']);
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
        Schema::dropIfExists('email_templates');
    }
}
