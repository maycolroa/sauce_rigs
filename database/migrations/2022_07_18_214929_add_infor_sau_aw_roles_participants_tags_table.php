<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\IndustrialSecure\WorkAccidents\TagsRolesParticipant;

class AddInforSauAwRolesParticipantsTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $roles = ['Jefe inmediato del trabajador o del área', 'Representante del COPASST / Vigía', 'Encargado del desarrollo del SG-SST', 'Profesional con licencia en Salud Ocupacional (propio o contratado)', 'Encargado del diseño de normas, procesos y/o mantenimiento.'];

        foreach ($roles as $key => $value) 
        {
            $record = new TagsRolesParticipant;
            $record->name = $value;
            $record->system = true;
            $record->save();
        }
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
