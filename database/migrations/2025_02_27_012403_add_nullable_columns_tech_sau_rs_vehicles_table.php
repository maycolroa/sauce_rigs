<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNullableColumnsTechSauRsVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_rs_vehicles', function (Blueprint $table) {   
            $table->string('mechanical_tech_number')->nullable()->change();
            $table->string('issuing_entity')->nullable()->change();
            $table->string('expedition_date_mechanical_tech')->nullable()->change();
            $table->string('due_date_mechanical_tech')->nullable()->change();
       });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
