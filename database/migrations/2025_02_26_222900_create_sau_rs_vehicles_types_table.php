<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\IndustrialSecure\RoadSafety\VehicleType;

class CreateSauRsVehiclesTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_rs_vehicles_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->timestamps();
        });

        $data = [
            'Automóviles',
            'Motocicletas',
            'Vehículos Pesados',
            'Camiones',
            'Montacargas',
            'Vehículos Eléctricos',
        ];

        foreach ($data as $key => $type) 
        {
            $record = VehicleType::firstOrCreate(
                ['name' => $type],
                ['name' => $type]
            );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_rs_vehicles_types');
    }
}
