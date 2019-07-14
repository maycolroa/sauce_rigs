<?php

use Illuminate\Database\Seeder;
use App\Models\General\Configuration;

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
            ],
            [
                'key' => 'days_alert_user_suspension',
                'value' => '60',
                'observation' => 'Cantidad de días para consultar si un usuario no ha iniciado sesión y enviar la notificación de alerta de suspensión'
            ],
            [
                'key' => 'days_user_suspension',
                'value' => '67',
                'observation' => 'SI el usuario no ha iniciado sesion en esta cantidad de dias sera supendido'
            ],
            [
                'key' => 'days_alert_expired_license',
                'value' => '30',
                'observation' => 'Cantidad de días para consultar si una licencia esta próxima a vencerse y enviar la notificación de alerta'
            ],
            [
                'key' => 'admin_license_notification_email',
                'value' => 'carolina.madrid@rigs.com.co',
                'observation' => 'Administrador al que se notificara sobre las licencias próximas a vencer'
            ]
        ];

        foreach ($data as $key => $value)
        {
            Configuration::updateOrCreate(['key' => $value['key']], $value);
        }
    }
}
