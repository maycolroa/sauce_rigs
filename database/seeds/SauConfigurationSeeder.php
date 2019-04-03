<?php

use Illuminate\Database\Seeder;
use App\Models\Configuration;

class SauConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'key' => 'biologicalmonitoring_audiometries_select_epp',
                'value' => '["Copa", "Moldeable", "Inserción"]',
                'observation' => 'Select para EPP de audiometrias'
            ],
            [
                'key' => 'biologicalmonitoring_audiometries_select_exposition_level',
                'value' => '["85 a 95 dB","No realizada","Menos de 80 dB","80 a 84.9 dB"]',
                'observation' => 'select para Nivel de exposicion de audiometrias'
            ],
            [
                'key' => 'action_plans_states',
                'value' => '["Pendiente","Ejecutada"]',
                'observation' => 'select para los estados de los planes de acción'
            ]
        ];

        foreach ($data as $key => $value)
        {
            Configuration::updateOrCreate(['key' => $value['key']], $value);
        }
    }
}
