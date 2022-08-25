<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
// use Doctrine\DBAL\Driver\PDOMySql\Driver;
// use DB;

class DropForeignKeyClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {   
            // \DB::statement("ALTER TABLE clients DROP FOREIGN KEY clients_user_id_foreign");
            // \DB::statement("ALTER TABLE `clients` CHANGE `user_id` `user_id` INT(10) NOT NULL DEFAULT '0'"); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
