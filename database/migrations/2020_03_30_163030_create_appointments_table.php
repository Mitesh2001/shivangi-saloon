<?php

use App\Models\Permission;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("external_id");
            $table->string("appointment_for");
            $table->string("description")->nullable();
            $table->nullableMorphs("source");
            $table->string('source_type_string')->nullable();
            // $table->string("color", 10);
            $table->integer('brnach_id')->index('brnach_id')->default(0);
            $table->integer('satus_id')->index('satus_id')->default(0);
            $table->integer('user_id')->index('user_id')->default(0);
            $table->integer('client_id')->index('client_id')->default(0);
            $table->string('contact_number', 10)->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->timestamp("start_at")->nullable();
            $table->timestamp("end_at")->nullable();
            
            $table->date('date')->nullable();
            $table->integer('created_by')->index('cerated_by')->default(0);
            $table->integer('updated_by')->index('updated_by')->default(0);

            $table->timestamps();
        });

        /** Create new permissions */
        $scpp = Permission::create([
            'display_name' => 'View calendar',
            'name' => 'calendar-view',
            'description' => 'Be able to view the calendar for appointments',
            'grouping' => 'appointment',
        ]);
        /** Create new permissions */
        $cpp = Permission::create([
            'display_name' => 'Add appointment',
            'name' => 'appointment-create',
            'description' => 'Be able to create a new appointment for a user',
            'grouping' => 'appointment',
        ]);

        /** Create new permissions */
        $epp = Permission::create([
            'display_name' => 'Edit appointment',
            'name' => 'appointment-edit',
            'description' => 'Be able to edit appointment such as times and title',
            'grouping' => 'appointment',
        ]);

        $dpp = Permission::create([
            'display_name' => 'Delete appointment',
            'name' => 'appointment-delete',
            'description' => 'Be able to delete an appointment',
            'grouping' => 'appointment',
        ]);

        $roles = \App\Models\Role::whereIn('name', ['owner', 'administrator'])->get();
        foreach ($roles as $role) {
            $role->permissions()->attach([$cpp->id, $dpp->id, $epp->id, $scpp->id]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('appointments');
    }
}
